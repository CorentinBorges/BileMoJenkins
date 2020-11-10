<?php

namespace App\Validator\Constraints;

use App\Repository\ClientRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TwoDecimalMaxValidator extends ConstraintValidator
{


    public function validate($value, Constraint $constraint)
    {
        if ((strlen($value) - strrpos($value, '.') - 1) > 2) {
            $propertyName = $this->context->getPropertyName();
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ property }}', $propertyName)
                ->addViolation();
        }
    }
}
