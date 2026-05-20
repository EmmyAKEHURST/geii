<?php

namespace App\Controller\Personnel;

use App\Entity\OffreAlternance;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Personnel\OffreAlternanceType;
use App\Repository\OffreAlternanceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Gestion des offres d'alternance proposées par les entreprises partenaires.
 *
 * Permet d'afficher, créer, modifier et supprimer les offres d'alternance.
 */
#[Route('/espace/personnel/offres-alternance')]
#[IsGranted('ROLE_PERSONNEL')]
final class OffreAlternanceController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly OffreAlternanceRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Affiche la liste complète des offres d'alternance.
     *
     * @description Cette liste est triée par ID en ordre décroissant (les plus récentes en premier).
     *
     * @return Response La page HTML de la liste des offres
     */
    #[Route('', name: 'app_espace_personnel_offres_alternance', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/offres-alternance.html.twig', [
            'staff' => $this->getStaffData(),
            'offers' => $this->repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * Crée et publie une nouvelle offre d'alternance.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_offres_alternance_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $offer = new OffreAlternance();
        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($offer);
            $this->em->flush();

            $this->addFlash('success', 'Offre publiée.');

            return $this->redirectToRoute('app_espace_personnel_offres_alternance');
        }

        return $this->render('espace/personnel/offres-alternance_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => true,
        ]);
    }

    /**
     * Modifie une offre d'alternance existante.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param OffreAlternance $offer L'offre à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_offres_alternance_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(OffreAlternance $offer, Request $request): Response
    {
        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Offre mise à jour.');

            return $this->redirectToRoute('app_espace_personnel_offres_alternance');
        }

        return $this->render('espace/personnel/offres-alternance_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => false,
        ]);
    }

    /**
     * Supprime une offre d'alternance.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param OffreAlternance $offer L'offre à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des offres
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_offres_alternance_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(OffreAlternance $offer, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-offre-' . $offer->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($offer);
        $this->em->flush();

        $this->addFlash('success', 'Offre supprimée.');

        return $this->redirectToRoute('app_espace_personnel_offres_alternance');
    }
}
