<?php

namespace App\DTO\Client;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;

abstract class ClientFromRequestInput
{
    /**
     * @var string
     * @Assert\NotNull()
     * @AcmeAssert\IsUnique()
     * @Assert\NotBlank(message="Client must have a username")
     * @Assert\Type(type="string", message="Userame has to be string type")
     * @Assert\Length(max="64",maxMessage="Username can't exceed 64 characters")
     */
    public $username;

    /**
     * @var string
     * @Assert\NotNull()
     * @AcmeAssert\IsUnique()
     * @Assert\NotBlank(message="Client must have a username")
     * @Assert\Type(type="string", message="Name has to be string type")
     * @Assert\Length(max="64",maxMessage="Name can't exceed 64 characters")
     */
    public $name;

    /**
     * @var string
     * @AcmeAssert\IsUnique()
     * @Assert\NotNull()
     * @Assert\Email(message="mail not valid")
     * @Assert\NotBlank(message="Client must have a mail")
     */
    public $mail;

    /**
     * @var string
     * @AcmeAssert\IsUnique()
     * @Assert\NotNull()
     * @Assert\Type(type="string", message="Phone number has to be string type")
     * @Assert\NotBlank(message="Client must have a phoneNumber")
     * @Assert\Regex (pattern="#[0-9]#", message="Phone number can just have...numbers!")
     */
    public $phoneNumber;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank(message="Client must have a password")
     * @Assert\Type(type="string")
     * @Assert\Length (
     *     max="50",
     *     maxMessage="password can't exceed 50 words",
     *     min="8",
     *     minMessage="password must contain at least 8 characters")
     * @Assert\Regex(
     *     pattern="#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+#",
     *     message="Password must have at least one Uppercase, one lowercase and one number")
     */
    public $password;
}
