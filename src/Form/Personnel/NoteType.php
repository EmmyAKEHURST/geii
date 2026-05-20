<?php

namespace App\Form\Personnel;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valeur', NumberType::class, [
                'label' => 'Note (/20)',
                'scale' => 2,
                'html5' => true,
                'attr' => ['min' => 0, 'max' => 20, 'step' => '0.25'],
            ])
            ->add('etudiant', EntityType::class, [
                'label' => 'Étudiant',
                'class' => Etudiant::class,
                'choice_label' => fn (Etudiant $e): string => sprintf('%s %s (%s)', $e->getNom(), $e->getPrenom(), $e->getNumEtudiant()),
                'placeholder' => '— Sélectionner un étudiant —',
                'required' => false,
                'query_builder' => fn ($r) => $r->createQueryBuilder('e')->orderBy('e.nom', 'ASC')->addOrderBy('e.prenom', 'ASC'),
            ])
            ->add('matiere', EntityType::class, [
                'label' => 'Matière',
                'class' => Matiere::class,
                'choice_label' => 'nom',
                'placeholder' => '— Sélectionner une matière —',
                'required' => false,
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['rows' => 3],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class
        ]);
    }
}
