<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseEntityController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var ValidatorInterface
     */
    protected $validator;
    /**
     * @var Security
     */
    protected $security;

    /**
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param Security $security
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        Security $security
    ) {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
    }
}
