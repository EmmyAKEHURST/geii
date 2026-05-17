<?php

namespace App\Form\Personnel;

use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('siret', TextType::class, ['label' => 'SIRET'])
            ->add('secteur', TextType::class, ['label' => 'Secteur d\'activité'])
            ->add('adresse', TextType::class, ['label' => 'Adresse']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Entreprise::class]);
    }
}
