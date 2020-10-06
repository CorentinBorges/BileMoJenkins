<?php


namespace App\Controller;

use App\DTO\Client\CreateClient\CreateClientFromRequestInput;
use App\DTO\Client\UpdateClient\UpdateClientFromRequestInput;
use App\Entity\Client;
use App\Helper\ViolationBuilder;
use App\Repository\ClientRepository;
use App\Responder\JsonResponder;
use Couchbase\RegexpSearchQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
     * @param Request $request
     * @return Response
     */
    public function createClient(Request $request)
    {
        /**
         * @var CreateClientFromRequestInput $clientDTO
         */
        $clientDTO = $this->serializer->deserialize(
            $request->getContent(),
            CreateClientFromRequestInput::class,
            'json',[AbstractNormalizer::IGNORED_ATTRIBUTES=>['roles','password']]
        );

        $errors = $this->validator->validate($clientDTO);
        if ($errors->count() > 0) {
            $errorList = ViolationBuilder::build($errors);
            return JsonResponder::responder(json_encode($errorList),Response::HTTP_BAD_REQUEST);
        }

        $client = Client::createClientFromRequest($clientDTO);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return JsonResponder::responder(
            null,
            Response::HTTP_CREATED,
            ['Location'=>'/api/clients/'.$client->getId()]
        );

    }

    /**
     * @Route ("/clients/{id}",name="update_client",methods={"PUT"})
     * @param Client $client
     * @param Request $request
     */
    public function updateClient(Client $client, Request $request)
    {
        $clientDTO = new UpdateClientFromRequestInput();
        $clientDTO->setId($client->getId());
        $newClient = $this->serializer->deserialize(
            $request->getContent(),
            UpdateClientFromRequestInput::class,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES=>['roles','id','password'],
            AbstractNormalizer::OBJECT_TO_POPULATE=>$clientDTO]);

        $errors = $this->validator->validate($newClient);
        if ($errors->count() > 0) {
            $errorsList=ViolationBuilder::build($errors);
            return JsonResponder::responder(json_encode($errorsList), Response::HTTP_BAD_REQUEST);
        }

        $client->updateClientFromRequest($clientDTO);
        $this->entityManager->flush();

        return JsonResponder::responder(null);
    }

    /**
     * @Route("/clients",name="client_list",methods={"GET"})
     * @return Response
     */
    public function clientList()
    {
        $all = $this->clientRepository->findAll();
        $list = $this->serializer->serialize($all, 'json',['groups'=>'list_all']);
        return JsonResponder::responder($list);
    }

    /**
     * @Route("/clients/{id}",name="client_details", methods={"GET"})
     * @param Client $client
     * @return Response
     */
    public function clientDetails(Client $client)
    {
        $clientDetails = $this->serializer->serialize($client, 'json',['groups'=>'details']);
        return JsonResponder::responder($clientDetails);
    }

    /**
     * @Route("/clients/{id}",name="delete_client",methods={"DELETE"})
     * @param Client $client
     * @return Response
     */
    public function clientDelete(Client $client)
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();
        return JsonResponder::responder(null);
    }
}