<?php


namespace App\Controller;

use App\DTO\Client\CreateClientFromRequestInput;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Responder\JsonResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClientController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;


    /**
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ClientRepository $clientRepository
    )
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/clients",name="create_client",methods={"POST"})
     */
    public function createClient(Request $request)
    {
        /**
         * @var CreateClientFromRequestInput $clientDTO
         */
        $clientDTO = $this->serializer->deserialize(
            $request->getContent(),
            CreateClientFromRequestInput::class,
            'json'
        );

        $client = Client::createClientFromRequest($clientDTO);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return JsonResponder::responder(
            null,
            Response::HTTP_CREATED,
            ['Location'=>'/api/client'.$client->getId()]
        );

    }

}

//todo: verify response location