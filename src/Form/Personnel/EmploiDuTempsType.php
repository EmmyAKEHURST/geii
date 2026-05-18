<?php

namespace App\Form\Personnel;

use App\Entity\EmploiDuTemps;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmploiDuTempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matiere', EntityType::class, [
                'label' => 'Matière',
                'class' => Matiere::class,
                'choice_label' => 'nom',
                'placeholder' => '— Sélectionner une matière —',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Début',
                'widget' => 'single_text',
            ])
            ->add('dateHeureFin', DateTimeType::class, [
                'label' => 'Fin',
                'widget' => 'single_text',
            ])
            ->add('salle', TextType::class, [
                'label' => 'Salle'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmploiDuTemps::class
        ]);
    }
}
