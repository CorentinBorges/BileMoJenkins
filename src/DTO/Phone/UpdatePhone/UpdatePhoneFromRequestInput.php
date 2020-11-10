<?php

namespace App\DTO\Phone\UpdatePhone;

use App\DTO\Phone\PhoneFromRequestInput;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;

class UpdatePhoneFromRequestInput extends PhoneFromRequestInput
{

    public $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
}
