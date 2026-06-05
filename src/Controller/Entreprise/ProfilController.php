<?php

namespace App\Controller\Entreprise;

use App\Form\Entreprise\ProfilEntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/entreprise/profil')]
#[IsGranted('ROLE_ENTREPRISE')]
final class ProfilController extends AbstractController
{
    use EntrepriseContextTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'app_espace_entreprise_profil', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $entreprise = $this->requireEntreprise();
        $form = $this->createForm(ProfilEntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Informations enregistrées.');

            return $this->redirectToRoute('app_espace_entreprise_profil');
        }

        return $this->render('espace/entreprise/profil.html.twig', [
            'company' => $this->getCompanyContext(),
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }
}
