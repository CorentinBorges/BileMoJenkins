<?php

namespace App\Helper;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationBuilder
{
    public static function build(ConstraintViolationListInterface $list)
    {
        $errors = [];

        /**@var ConstraintViolationInterface $violation */
        foreach ($list as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $errors;
    }
}
