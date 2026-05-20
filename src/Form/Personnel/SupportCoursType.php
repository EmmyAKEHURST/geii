<?php

namespace App\Form\Personnel;

use App\Entity\SupportCours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Formulaire de support de cours. Le champ « fichier » n'est pas mappé : le
 * contrôleur reçoit l'UploadedFile, le déplace dans le répertoire public puis
 * stocke le chemin dans `fichier_path`.
 *
 * Contraintes alignées sur le CDC §3.2 : PDF uniquement, 10 Mo max.
 */
class SupportCoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('dateDepot', DateType::class, [
                'label' => 'Date de dépôt',
                'widget' => 'single_text',
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier PDF',
                'mapped' => false,
                'required' => $options['is_new'],
                'help' => 'Format PDF, 10 Mo max.',
                'constraints' => [
                    new File(
                        maxSize: '10M',
                        mimeTypes: ['application/pdf', 'application/x-pdf'],
                        mimeTypesMessage: 'Veuillez fournir un document PDF valide.',
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportCours::class,
            'is_new' => false,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
    }
}
