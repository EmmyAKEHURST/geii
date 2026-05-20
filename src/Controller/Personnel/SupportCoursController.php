<?php

namespace App\Controller\Personnel;

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
 * CRUD des supports de cours (PDF).
 *
 * Sécurité (CDC §4.2) :
 *  - les fichiers sont stockés sous %kernel.project_dir%/var/share/supports
 *    (hors du document root) ;
 *  - le téléchargement passe par une route authentifiée
 *    (#[IsGranted('ROLE_PERSONNEL')]) qui sert le binaire.
 */
#[Route('/espace/personnel/supports')]
#[IsGranted('ROLE_PERSONNEL')]
final class SupportCoursController extends AbstractController
{
    use StaffContextTrait;

    /**
     * Répertoire auquel les supports de cours vont être stockées.
     */
    private const string RELATIVE_UPLOAD_DIR = 'share/supports';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SupportCoursRepository $repository,
    ) {}

    /**
     * Affiche la liste complète des supports de cours.
     *
     * @description Cette liste est triée par date de dépôt en ordre décroissant.
     *
     * @return Response La page HTML de la liste des supports
     */
    #[Route('', name: 'app_espace_personnel_supports', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/supports.html.twig', [
            'staff' => $this->getStaffData(),
            'supports' => $this->repository->findBy([], ['date_depot' => 'DESC']),
        ]);
    }

    /**
     * Crée et dépose un nouveau support de cours.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     * Le fichier PDF est stocké de manière sécurisée hors du document root.
     *
     * @param Request $request La requête HTTP
     * @param SluggerInterface $slugger Service pour générer des slugs sécurisés
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_supports_new', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_espace_personnel_supports');
        }

        return $this->render('espace/personnel/supports_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'support' => $support,
            'isNew' => true,
        ]);
    }

    /**
     * Modifie un support de cours existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     * Un nouveau fichier peut optionnellement être fourni pour remplacer l'ancien.
     *
     * @param SupportCours $support Le support à modifier
     * @param Request $request La requête HTTP
     * @param SluggerInterface $slugger Service pour générer des slugs sécurisés
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_supports_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
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

            return $this->redirectToRoute('app_espace_personnel_supports');
        }

        return $this->render('espace/personnel/supports_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'support' => $support,
            'isNew' => false,
        ]);
    }

    /**
     * Supprime un support de cours et son fichier associé.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     * Le fichier stocké est supprimé du disque.
     *
     * @param SupportCours $support Le support à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des supports
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_supports_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
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

        return $this->redirectToRoute('app_espace_personnel_supports');
    }

    /**
     * Télécharge un support de cours en tant que pièce jointe.
     *
     * @description Vérifie que le fichier existe avant de le servir.
     *
     * @param SupportCours $support Le support à télécharger
     * @return BinaryFileResponse La réponse binaire du fichier
     */
    #[Route('/{id}/download', name: 'app_espace_personnel_supports_download', methods: ['GET'], requirements: ['id' => '\d+'])]
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

    /**
     * Stocke un fichier uploadé dans le système de fichiers.
     *
     * @description Génère un nom unique en utilisant un slug du nom original et un identifiant unique.
     * Crée le répertoire de destination s'il n'existe pas avec les permissions appropriées.
     *
     * @param UploadedFile $file
     * @param SluggerInterface $slugger
     * @return string Le nom du fichier stocké
     */
    private function storeFile(UploadedFile $file, SluggerInterface $slugger): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $slugger->slug($original)->lower();
        $newName = sprintf('%s-%s.%s', $safeName, uniqid(), $file->guessExtension() ?: 'pdf');

        $dir = $this->getUploadAbsoluteDir();

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new \RuntimeException(sprintf('Impossible de créer le dossier "%s".', $dir));
        }

        $file->move($dir, $newName);

        return $newName;
    }

    /**
     * Supprime un fichier du système de fichiers s'il existe.
     *
     * @description Effectue une suppression silencieuse si le chemin fourni est null ou vide.
     * Ignore les erreurs de suppression si le fichier a déjà été supprimé.
     *
     * @param string|null $relativePath Chemin relatif du fichier à supprimer
     * @return void
     */
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

    /**
     * Récupère le chemin absolu du répertoire de stockage des fichiers uploadés.
     *
     * @description Construit le chemin en combinant le répertoire du projet (kernel.project_dir),
     * le répertoire var, et le répertoire relatif configuré (share/supports).
     *
     * @return string Le chemin absolu du répertoire de stockage
     */
    private function getUploadAbsoluteDir(): string
    {
        /** @var string $path */
        $path = $this->getParameter('kernel.project_dir');

        return $path . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . self::RELATIVE_UPLOAD_DIR;
    }
}
