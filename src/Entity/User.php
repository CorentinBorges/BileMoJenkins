<?php

namespace App\Entity;

use App\DTO\User\CreateUser\CreateUserFromFixture;
use App\DTO\User\CreateUser\CreateUserFromRequestInput;
use App\DTO\User\UpdateUser\UpdateUserFromRequestInput;
use App\Repository\ClientRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * Class Users
 * @package App\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @Groups({"list_users"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     * @Groups({"user_details"})
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Groups({"list_users","user_details"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"list_users","user_details"})
     */
    private $email;

    /**
     *
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct(string $fullName, string $username, string $email, Client $client)
    {
        $this->id = Uuid::v4()->__toString();
        $this->fullName = $fullName;
        $this->username = $username;
        $this->email = $email;
        $this->client = $client;
        $this->roles = ['ROLE_USER'];
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getClient(): Client
    {
        return $this->client;
    }

    public static function createUserFromRequest(
        CreateUserFromRequestInput $userDTO,
        ClientRepository $clientRepository
    ) {
        $client = User::setClientWithId($userDTO->getClientId(), $clientRepository);
        return new self(
            $userDTO->fullName,
            $userDTO->username,
            $userDTO->email,
            $client
        );
    }

    public static function createFromFixture(CreateUserFromFixture $userDTO)
    {
        return new self(
            $userDTO->fullName,
            $userDTO->username,
            $userDTO->email,
            $userDTO->client
        );
    }

    public static function setClientWithId(string $clientId, ClientRepository $clientRepository)
    {
        if (! $clientRepository->findOneBy(['id' => $clientId])) {
            throw new EntityNotFoundException(
                'Client with the id: ' . $clientId . ' not found'
            );
        }
        /**
         * @var Client $client
         */
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        return $client;
    }

    public function updateUserFromRequest(UpdateUserFromRequestInput $userDTO)
    {
        $this->username = $userDTO->username;
        $this->fullName = $userDTO->fullName;
        $this->email = $userDTO->email;
    }
}
