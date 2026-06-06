<?php

namespace App\Controller\Enseignant;

use App\Entity\SupportCours;
use App\Form\Personnel\SupportCoursType;
use App\Repository\SupportCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Gestion des supports de cours (PDF) depuis l'espace enseignant.
 *
 * Contraintes (CDC §3.2) :
 *  - PDF uniquement, 10 Mo max.
 *  - Les fichiers sont stockés sous %kernel.project_dir%/var/share/supports
 *    (hors du document root).
 *  - Le téléchargement passe par une route authentifiée.
 */
#[Route('/espace/enseignant/supports')]
#[IsGranted('ROLE_ENSEIGNANT')]
final class SupportCoursController extends AbstractController
{
    use TeacherContextTrait;

    private const string RELATIVE_UPLOAD_DIR = 'share/supports';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SupportCoursRepository $repository,
    ) {}

    /**
     * Affiche la liste complète des supports, triée par date de dépôt décroissante.
     */
    #[Route('', name: 'app_espace_enseignant_supports', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/enseignant/supports.html.twig', [
            'teacher'  => $this->getTeacherData(),
            'supports' => $this->repository->findBy([], ['date_depot' => 'DESC']),
        ]);
    }

    /**
     * Dépose un nouveau support de cours (PDF, 10 Mo max).
     *
     * Affiche le formulaire en GET et traite la soumission en POST.
     * Le fichier est stocké de manière sécurisée hors du document root.
     */
    #[Route('/new', name: 'app_espace_enseignant_supports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $support = new SupportCours();
        $form = $this->createForm(SupportCoursType::class, $support, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('fichier')->getData();

            if ($file !== null) {
                $support->setFichierPath($this->storeFile($file, $slugger));
            }

            $this->em->persist($support);
            $this->em->flush();

            $this->addFlash('success', 'Support déposé.');

            return $this->redirectToRoute('app_espace_enseignant_supports');
        }

        return $this->render('espace/enseignant/supports_form.html.twig', [
            'teacher' => $this->getTeacherData(),
            'form'    => $form,
            'support' => $support,
            'isNew'   => true,
        ]);
    }

    /**
     * Modifie un support existant.
     *
     * Un nouveau fichier peut optionnellement remplacer l'ancien.
     */
    #[Route('/{id}/edit', name: 'app_espace_enseignant_supports_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(SupportCours $support, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SupportCoursType::class, $support);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('fichier')->getData();

            if ($file !== null) {
                $this->deleteFileIfExists($support->getFichierPath());
                $support->setFichierPath($this->storeFile($file, $slugger));
            }

            $this->em->flush();
            $this->addFlash('success', 'Support mis à jour.');

            return $this->redirectToRoute('app_espace_enseignant_supports');
        }

        return $this->render('espace/enseignant/supports_form.html.twig', [
            'teacher' => $this->getTeacherData(),
            'form'    => $form,
            'support' => $support,
            'isNew'   => false,
        ]);
    }

    /**
     * Supprime un support et son fichier associé.
     *
     * Nécessite un token CSRF valide.
     */
    #[Route('/{id}/delete', name: 'app_espace_enseignant_supports_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(SupportCours $support, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-support-' . $support->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->deleteFileIfExists($support->getFichierPath());

        $this->em->remove($support);
        $this->em->flush();

        $this->addFlash('success', 'Support supprimé.');

        return $this->redirectToRoute('app_espace_enseignant_supports');
    }

    /**
     * Télécharge un support de cours en tant que pièce jointe.
     */
    #[Route('/{id}/download', name: 'app_espace_enseignant_supports_download', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function download(SupportCours $support): BinaryFileResponse
    {
        $absolutePath = $this->getUploadAbsoluteDir() . DIRECTORY_SEPARATOR . $support->getFichierPath();

        if (!is_file($absolutePath)) {
            throw $this->createNotFoundException('Fichier introuvable.');
        }

        $response = new BinaryFileResponse($absolutePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $support->getTitre() . '.pdf'
        );

        return $response;
    }

    private function storeFile(UploadedFile $file, SluggerInterface $slugger): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $slugger->slug($original)->lower();
        $newName  = sprintf('%s-%s.%s', $safeName, uniqid(), $file->guessExtension() ?: 'pdf');

        $dir = $this->getUploadAbsoluteDir();

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new \RuntimeException(sprintf('Impossible de créer le dossier "%s".', $dir));
        }

        $file->move($dir, $newName);

        return $newName;
    }

    private function deleteFileIfExists(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $absolute = $this->getUploadAbsoluteDir() . DIRECTORY_SEPARATOR . $relativePath;

        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    private function getUploadAbsoluteDir(): string
    {
        /** @var string $path */
        $path = $this->getParameter('kernel.project_dir');

        return $path . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . self::RELATIVE_UPLOAD_DIR;
    }
}
