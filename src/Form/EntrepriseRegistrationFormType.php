<?php

namespace App\Form;

use App\Entity\Compte;
use App\Validator\ComptePasswordConstraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EntrepriseRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountType', ChoiceType::class, [
                'mapped'      => false,
                'label'       => 'Je suis',
                'placeholder' => '— Sélectionner —',
                'choices'     => [
                    'Une entreprise' => 'entreprise',
                    'Un étudiant'    => 'etudiant',
                    'Un enseignant'  => 'enseignant',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner votre profil.'),
                ],
            ])
            ->add('email')
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options'   => [
                    'label' => 'Mot de passe',
                    'attr'  => ['autocomplete' => 'new-password'],
                ],
                'second_options'  => [
                    'label' => 'Confirmer le mot de passe',
                    'attr'  => ['autocomplete' => 'new-password'],
                ],
                'constraints' => ComptePasswordConstraints::rules(),
            ])

            // ── Champs entreprise ──────────────────────────────────────────
            ->add('nomEntreprise', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Nom',
            ])
            ->add('siret', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Numéro SIRET',
            ])
            ->add('secteur', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => "Secteur d'activité",
            ])
            ->add('adresse', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Adresse',
            ])

            // ── Champs étudiant ────────────────────────────────────────────
            ->add('numEtudiant', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Numéro étudiant',
            ])
            ->add('nomEtudiant', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Nom',
            ])
            ->add('prenomEtudiant', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Prénom',
            ])

            // ── Champs enseignant ──────────────────────────────────────────
            ->add('nomEnseignant', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Nom',
            ])
            ->add('prenomEnseignant', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Prénom',
            ])
            ->add('specialite', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Spécialité',
            ])
            ->add('bureau', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Bureau',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
