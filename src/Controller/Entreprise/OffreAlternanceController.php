<?php

namespace App\Controller\Entreprise;

use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use App\Form\Entreprise\OffreAlternanceType;
use App\Repository\OffreAlternanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/entreprise/offres-alternance')]
#[IsGranted('ROLE_ENTREPRISE')]
final class OffreAlternanceController extends AbstractController
{
    use EntrepriseContextTrait;

    public function __construct(
        private readonly OffreAlternanceRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'app_espace_entreprise_offres_alternance', methods: ['GET'])]
    public function index(): Response
    {
        $entreprise = $this->requireEntreprise();

        return $this->render('espace/entreprise/offres-alternance.html.twig', [
            'company' => $this->getCompanyContext(),
            'offers' => $this->repository->findByEntrepriseOrdered($entreprise),
        ]);
    }

    #[Route('/new', name: 'app_espace_entreprise_offres_alternance_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $offer = (new OffreAlternance())
            ->setEntreprise($entreprise)
            ->setStatut(StatutAlternance::ACTIVE);

        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setEntreprise($entreprise);
            $this->em->persist($offer);
            $this->em->flush();

            $this->addFlash('success', 'Offre publiée.');

            return $this->redirectToRoute('app_espace_entreprise_offres_alternance');
        }

        return $this->render('espace/entreprise/offres-alternance_form.html.twig', [
            'company' => $this->getCompanyContext(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_entreprise_offres_alternance_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(OffreAlternance $offer, Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $this->assertOfferOwnedByEntreprise($offer, $entreprise);

        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setEntreprise($entreprise);
            $this->em->flush();

            $this->addFlash('success', 'Offre mise à jour.');

            return $this->redirectToRoute('app_espace_entreprise_offres_alternance');
        }

        return $this->render('espace/entreprise/offres-alternance_form.html.twig', [
            'company' => $this->getCompanyContext(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_entreprise_offres_alternance_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(OffreAlternance $offer, Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $this->assertOfferOwnedByEntreprise($offer, $entreprise);

        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-offre-' . $offer->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($offer);
        $this->em->flush();

        $this->addFlash('success', 'Offre supprimée.');

        return $this->redirectToRoute('app_espace_entreprise_offres_alternance');
    }
}
