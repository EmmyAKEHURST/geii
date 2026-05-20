<?php

namespace App\Form\Personnel;

use App\Entity\Compte;
use App\Entity\Enseignant;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Compte $currentCompte */
        $currentCompte = $options['current_compte'];

        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('specialite', TextType::class, ['label' => 'Spécialité'])
            ->add('bureau', TextType::class, ['label' => 'Bureau'])
            ->add('compte', EntityType::class, [
                'label' => 'Compte utilisateur lié',
                'class' => Compte::class,
                'choice_label' => 'email',
                'placeholder' => '— Aucun compte associé —',
                'required' => false,
                'help' => 'Si lié, ROLE_ENSEIGNANT sera automatiquement ajouté au compte.',
                'query_builder' => fn (EntityRepository $r) => $this->availableComptesQb($r, $currentCompte),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
            'current_compte' => null,
        ]);
        $resolver->setAllowedTypes('current_compte', [Compte::class, 'null']);
    }

    /**
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
