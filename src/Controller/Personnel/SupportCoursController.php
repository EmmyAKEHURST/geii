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

    private const RELATIVE_UPLOAD_DIR = 'share/supports';

    #[Route('', name: 'app_espace_personnel_supports', methods: ['GET'])]
    public function index(SupportCoursRepository $repository): Response
    {
        return $this->render('espace/personnel/supports.html.twig', [
            'staff' => $this->getStaffData(),
            'supports' => $repository->findBy([], ['date_depot' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_supports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
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

            $em->persist($support);
            $em->flush();
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

    #[Route('/{id}/edit', name: 'app_espace_personnel_supports_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(SupportCours $support, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
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
            $em->flush();
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

    #[Route('/{id}/delete', name: 'app_espace_personnel_supports_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(SupportCours $support, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-support-' . $support->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->deleteFileIfExists($support->getFichierPath());
        $em->remove($support);
        $em->flush();
        $this->addFlash('success', 'Support supprimé.');

        return $this->redirectToRoute('app_espace_personnel_supports');
    }

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

    private function storeFile(UploadedFile $file, SluggerInterface $slugger): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $slugger->slug($original)->lower();
        $newName = sprintf('%s-%s.%s', $safeName, uniqid(), $file->guessExtension() ?: 'pdf');

        $dir = $this->getUploadAbsoluteDir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
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
        return $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . self::RELATIVE_UPLOAD_DIR;
    }
}
