<?php

namespace App\DTO\User\CreateUser;

use App\DTO\User\UserFromRequestInput;
use App\Entity\Client;

class CreateUserFromFixture extends UserFromRequestInput
{
    /**
     * @var Client $client
     */
    public $client;
}
