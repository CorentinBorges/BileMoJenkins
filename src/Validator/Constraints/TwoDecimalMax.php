<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TwoDecimalMax extends Constraint
{
    public $message = 'Value of {{ property }} can\' have more than 2 decimals';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
