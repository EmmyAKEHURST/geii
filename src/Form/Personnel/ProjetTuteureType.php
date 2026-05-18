<?php

namespace App\Form\Personnel;

use App\Entity\Enseignant;
use App\Entity\Entreprise;
use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetTuteureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('annee', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['min' => 2000, 'max' => 2100],
            ])
            ->add('entreprise', EntityType::class, [
                'label' => 'Entreprise commanditaire',
                'class' => Entreprise::class,
                'choice_label' => 'nom',
                'placeholder' => '— Aucune entreprise —',
                'required' => false,
                'query_builder' => fn ($r) => $r->createQueryBuilder('e')->orderBy('e.nom', 'ASC'),
            ])
            ->add('enseignantTuteur', EntityType::class, [
                'label' => 'Enseignant tuteur',
                'class' => Enseignant::class,
                'choice_label' => fn (Enseignant $e): string => trim(($e->getNom() ?? '') . ' ' . ($e->getPrenom() ?? '')),
                'placeholder' => '— Aucun tuteur assigné —',
                'required' => false,
                'query_builder' => fn ($r) => $r->createQueryBuilder('e')->orderBy('e.nom', 'ASC')->addOrderBy('e.prenom', 'ASC'),
            ])
            ->add('statut', EnumType::class, [
                'label' => 'Statut',
                'class' => StatutProjetTuteure::class,
                'choice_label' => fn (StatutProjetTuteure $s): string => match ($s) {
                    StatutProjetTuteure::OUVERT   => 'Ouvert',
                    StatutProjetTuteure::EN_COURS => 'En cours',
                    StatutProjetTuteure::TERMINE  => 'Terminé',
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjetTuteure::class
        ]);
    }
}
