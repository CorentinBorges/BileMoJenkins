<?php

namespace App\DTO\User\CreateUser;

use App\DTO\User\UserFromRequestInput;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserFromRequestInput extends UserFromRequestInput
{
    public $clientId;

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId): void
    {
        $this->clientId = $clientId;
    }
}
