<?php

namespace App\Entity;

use App\DTO\Phone\CreatePhone\CreatePhoneFromRequestInput;
use App\DTO\Phone\UpdatePhone\UpdatePhoneFromRequestInput;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * Class Phone
 * @package App\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\PhoneRepository")
 */
class Phone
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @OA\Property (description="Phone's id")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Groups({"list_phone","detail_phone"})
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=60)
     * @Groups({"list_phone","detail_phone"})
     */
    private $model;

    /**
     * @var float
     *
     * @ORM\Column(type="float",precision= 6, scale = 2)
     * @Groups({"list_phone","detail_phone"})
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=50)
     * @Groups({"detail_phone"})
     */
    private $system;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=3, scale=2)
     * @Groups({"detail_phone"})
     */
    private $screenSize;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Groups({"detail_phone"})
     */
    private $storage;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     * @Groups({"detail_phone"})
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column (type="text")
     * @Groups({"detail_phone"})
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $createdAt;

    public function __construct(
        string $brand,
        string $model,
        float $price,
        string $system,
        float $screenSize,
        int $storage,
        string $color,
        string $description
    ) {
        $this->id = Uuid::v4()->__toString();
        $this->brand = $brand;
        $this->model = $model;
        $this->price = $price;
        $this->system = $system;
        $this->screenSize = $screenSize;
        $this->storage = $storage;
        $this->color = $color;
        $this->description = $description;
        $this->createdAt = time();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getSystem(): string
    {
        return $this->system;
    }

    public function getScreenSize(): int
    {
        return $this->screenSize;
    }

    public function getStorage(): int
    {
        return $this->storage;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public static function createFromRequest(CreatePhoneFromRequestInput $phoneDTO)
    {
        return new self(
            $phoneDTO->brand,
            $phoneDTO->model,
            $phoneDTO->price,
            $phoneDTO->system,
            $phoneDTO->screenSize,
            $phoneDTO->storage,
            $phoneDTO->color,
            $phoneDTO->description
        );
    }

    public function updateFromRequest(UpdatePhoneFromRequestInput $phoneDTO)
    {
        $this->brand = $phoneDTO->brand;
        $this->model = $phoneDTO->model;
        $this->price = $phoneDTO->price;
        $this->system = $phoneDTO->system;
        $this->storage = $phoneDTO->storage;
        $this->screenSize = $phoneDTO->screenSize;
        $this->color = $phoneDTO->color;
        $this->description = $phoneDTO->description;
    }
}
