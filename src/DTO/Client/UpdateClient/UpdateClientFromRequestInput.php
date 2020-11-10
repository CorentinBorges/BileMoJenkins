<?php

namespace App\DTO\Client\UpdateClient;

use App\DTO\Client\ClientFromRequestInput;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateClientFromRequestInput extends ClientFromRequestInput
{
    public $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
