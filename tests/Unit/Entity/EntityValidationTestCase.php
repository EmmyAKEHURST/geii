<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Classe de base pour les tests unitaires de validation des entités.
 *
 * Remplace le validateur UniqueEntity par un no-op afin d'éviter
 * la dépendance à Doctrine dans les tests purement unitaires.
 * UniqueEntity est testé dans les tests d'intégration (IntegrationTestCase).
 */
abstract class EntityValidationTestCase extends TestCase
{
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        $factory = new class extends ConstraintValidatorFactory
        {
            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                if ($constraint instanceof UniqueEntity) {
                    return new class implements ConstraintValidatorInterface
                    {
                        public function initialize(ExecutionContextInterface $context): void
                        {
                            // ...
                        }

                        public function validate(mixed $value, Constraint $constraint): void
                        {
                            // ...
                        }
                    };
                }

                return parent::getInstance($constraint);
            }
        };

        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory($factory)
            ->getValidator()
        ;
    }
}
