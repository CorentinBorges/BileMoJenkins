<?php

namespace App\Validator\Constraints;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueValidator extends ConstraintValidator
{

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }
    public function validate($value, Constraint $constraint)
    {
        $repository = $this->createRepository();
        $property = $this->context->getPropertyName();
        $object = $this->context->getObject();
        if ($userInDb = $repository->findOneBy([$property => $value])) {
            if (
                (method_exists($object, 'getId') && $object->getId() !== $userInDb->getId()) ||
                !method_exists($object, 'getId')
            ) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ property }}', $property)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }

    public function createRepository()
    {
        $pathClassTable = explode('\\', $this->context->getClassName());
        $entityName = $pathClassTable[2];
        $repositoryName = 'App\Repository\\' . $entityName . 'Repository';
        return new $repositoryName($this->managerRegistry);
    }
}
