<?php

namespace App\Form\Entreprise;

use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
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
            ->add('titre', TextType::class, ['label' => 'Titre du projet'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('annee', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['min' => 2000, 'max' => 2100],
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
            'data_class' => ProjetTuteure::class,
        ]);
    }
}
