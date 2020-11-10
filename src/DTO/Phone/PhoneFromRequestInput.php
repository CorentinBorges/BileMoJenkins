<?php

namespace App\DTO\Phone;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;

abstract class PhoneFromRequestInput
{
    /**
     * @var string
     *
     * @Assert\Length(max="50", maxMessage="The brand can't exceed 50 characters")
     * @Assert\Type(type="string", message="Brand has to be string type")
     * @Assert\NotBlank(message="You have to enter a brand")
     */
    public $brand;

    /**
     * @var string
     * @AcmeAssert\IsUnique()
     * @Assert\Length(max="60", maxMessage="The model can't exceed 60 characters")
     * @Assert\Type(type="string", message="Model has to be string type")
     * @Assert\NotBlank (message="You have to enter a model")
     */
    public $model;

    /**
     * @var float
     *
     *@Assert\Range(
     *     min="10.00",
     *     minMessage="The price can't be under 10",
     *     max="5000",
     *     maxMessage="The price can't exceed 5000"))
     * @AcmeAssert\TwoDecimalMax
     * @Assert\Type (type="float", message="Price has to be float type")
     * @Assert\NotBlank (message="You have to enter a price")
     */
    public $price;

    /**
     * @var string
     * @Assert\Type(type="string", message="System has to be string type")
     * @Assert\NotBlank (message="You have to enter a system name")
     */
    public $system;

    /**
     * @var float
     * @Assert\NotBlank (message="You have to enter a screenSize")
     * @Assert\Type(type="float", message="Screen size has to be a float type")
     * @Assert\Range(
     *     max="10",
     *     maxMessage="Screen size can't exceed 10",
     *     min="0",
     *     minMessage="Sreen size can't be under 0")
     * @AcmeAssert\TwoDecimalMax
     */
    public $screenSize;

    /**
     * @var int
     *
     * @Assert\NotBlank() (message="You have to enter a storage value")
     * @Assert\DivisibleBy(message="storage has to be divisible by 8", value="8")
     * @Assert\Type(type="integer", message="Storage has to be integer type")
     */
    public $storage;

    /**
     * @var string
     *
     * @Assert\Type(type="string", message="Color has to be string type")
     * @Assert\Length (max="16", maxMessage="Color can't exceed 16 characters")
     * @Assert\NotBlank (message="You have to enter a color")
     */
    public $color;

    /**
     * @var string
     *
     * @Assert\NotBlank (message="You have to enter a description")
     * @Assert\Type(type="string", message="Description has to be string type")
     */
    public $description;
}
