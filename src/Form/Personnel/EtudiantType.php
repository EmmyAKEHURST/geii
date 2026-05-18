<?php

namespace App\Form\Personnel;

use App\Entity\Compte;
use App\Entity\Etudiant;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire CRUD pour un Etudiant. Le numéro étudiant est la clé primaire
 * (string), il n'est éditable qu'à la création.
 *
 * Le sélecteur « compte » liste uniquement les comptes non encore rattachés
 * à un profil + le compte actuellement lié (édition).
 */
class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Compte $currentCompte */
        $currentCompte = $options['current_compte'];

        $builder
            ->add('numEtudiant', TextType::class, [
                'label' => 'N° étudiant',
                'help' => 'Identifiant unique (ex : E2025001). Non modifiable après création.',
                'disabled' => !$options['is_new'],
            ])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('annee', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['min' => 2000, 'max' => 2100],
            ])
            ->add('compte', EntityType::class, [
                'label' => 'Compte utilisateur lié',
                'class' => Compte::class,
                'choice_label' => 'email',
                'placeholder' => '— Aucun compte associé —',
                'required' => false,
                'help' => 'Si lié, ROLE_ETUDIANT sera automatiquement ajouté au compte.',
                'query_builder' => fn (EntityRepository $r) => $this->availableComptesQb($r, $currentCompte),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
            'is_new' => false,
            'current_compte' => null,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
        $resolver->setAllowedTypes('current_compte', [Compte::class, 'null']);
    }

    /**
     * Comptes proposables : aucun profil rattaché + (optionnellement) celui déjà lié à l'entité éditée.
     *
     * @param EntityRepository<Compte> $repository
     */
    private function availableComptesQb(EntityRepository $repository, ?Compte $current): QueryBuilder
    {
        $qb = $repository->createQueryBuilder('c')
            ->leftJoin('c.etudiant', 'e')
            ->leftJoin('c.enseignant', 'en')
            ->leftJoin('c.personnel', 'p')
            ->where('e IS NULL AND en IS NULL AND p IS NULL')
            ->orderBy('c.email', 'ASC')
        ;

        if ($current instanceof Compte && $current->getId() !== null) {
            $qb->orWhere('c.id = :curr')->setParameter('curr', $current->getId());
        }

        return $qb;
    }
}
