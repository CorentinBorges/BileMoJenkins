<?php

namespace App\DTO\User\UpdateUser;

use App\DTO\User\UserFromRequestInput;

class UpdateUserFromRequestInput extends UserFromRequestInput
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
