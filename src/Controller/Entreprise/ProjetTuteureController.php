<?php

namespace App\Controller\Entreprise;

use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
use App\Form\Entreprise\ProjetTuteureType;
use App\Repository\ProjetTuteureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/entreprise/projets-tuteures')]
#[IsGranted('ROLE_ENTREPRISE')]
final class ProjetTuteureController extends AbstractController
{
    use EntrepriseContextTrait;

    public function __construct(
        private readonly ProjetTuteureRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'app_espace_entreprise_projets_tuteures', methods: ['GET'])]
    public function index(): Response
    {
        $entreprise = $this->requireEntreprise();

        return $this->render('espace/entreprise/projets-tuteures.html.twig', [
            'company' => $this->getCompanyContext(),
            'projects' => $this->repository->findByEntrepriseOrdered($entreprise),
        ]);
    }

    #[Route('/new', name: 'app_espace_entreprise_projets_tuteures_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $project = (new ProjetTuteure())
            ->setEntreprise($entreprise)
            ->setStatut(StatutProjetTuteure::OUVERT)
            ->setAnnee((int) date('Y'));

        $form = $this->createForm(ProjetTuteureType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setEntreprise($entreprise);
            $this->em->persist($project);
            $this->em->flush();

            $this->addFlash('success', 'Projet publié.');

            return $this->redirectToRoute('app_espace_entreprise_projets_tuteures');
        }

        return $this->render('espace/entreprise/projets-tuteures_form.html.twig', [
            'company' => $this->getCompanyContext(),
            'form' => $form,
            'project' => $project,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_entreprise_projets_tuteures_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(ProjetTuteure $project, Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $this->assertProjectOwnedByEntreprise($project, $entreprise);

        $form = $this->createForm(ProjetTuteureType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setEntreprise($entreprise);
            $this->em->flush();

            $this->addFlash('success', 'Projet mis à jour.');

            return $this->redirectToRoute('app_espace_entreprise_projets_tuteures');
        }

        return $this->render('espace/entreprise/projets-tuteures_form.html.twig', [
            'company' => $this->getCompanyContext(),
            'form' => $form,
            'project' => $project,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_entreprise_projets_tuteures_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(ProjetTuteure $project, Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $this->assertProjectOwnedByEntreprise($project, $entreprise);

        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-projet-' . $project->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($project);
        $this->em->flush();

        $this->addFlash('success', 'Projet supprimé.');

        return $this->redirectToRoute('app_espace_entreprise_projets_tuteures');
    }
}
