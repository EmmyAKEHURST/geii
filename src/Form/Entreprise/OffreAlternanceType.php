<?php

namespace App\Form\Entreprise;

use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreAlternanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre du poste'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (mois)',
                'attr' => ['min' => 1, 'max' => 36],
            ])
            ->add('statut', EnumType::class, [
                'label' => 'Statut',
                'class' => StatutAlternance::class,
                'choice_label' => fn (StatutAlternance $s): string => match ($s) {
                    StatutAlternance::ACTIVE  => 'Active',
                    StatutAlternance::POURVUE => 'Pourvue',
                    StatutAlternance::EXPIREE => 'Expirée',
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreAlternance::class,
        ]);
    }
}
