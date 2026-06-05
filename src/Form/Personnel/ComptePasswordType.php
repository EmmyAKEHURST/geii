<?php

namespace App\Form\Personnel;

use App\Validator\ComptePasswordConstraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Formulaire de changement de mot de passe par le Personnel.
 *
 * Le hash est appliqué par le contrôleur via UserPasswordHasherInterface.
 */
class ComptePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'required' => true,
            'invalid_message' => 'Les mots de passe ne correspondent pas.',
            'first_options' => [
                'label' => 'Nouveau mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
            ],
            'second_options' => [
                'label' => 'Confirmer',
                'attr' => ['autocomplete' => 'new-password'],
            ],
            'constraints' => ComptePasswordConstraints::rules(),
        ]);
    }
}
