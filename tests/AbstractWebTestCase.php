<?php


namespace App\Tests;


use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Faker\Factory;
use Faker\Generator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $containerService;

    /**
     * @var KernelBrowser
     */
    protected $apiClient;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EncoderFactory
     */
    protected $encoderFactory;
    /**
     * @var JWTManager
     */
    protected $jwtManager;
    /**
     * @var Generator
     */
    protected $faker;
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    protected function setUp()
    {
        $this->apiClient = static::createClient();

        $this->containerService = self::$container;
        $this->entityManager = $this->containerService->get('doctrine.orm.entity_manager');
        $this->encoderFactory = $this->containerService->get('security.encoder_factory');

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

        $this->faker = Factory::create();
        $this->jwtManager = $this->containerService->get('lexik_jwt_authentication.jwt_manager');

        $this->loadClientFixture();
    }

    protected function request(string $methode, string $uri,? Client $client = null, $contentBody = null) :Response
    {
        if (isset($client)) {
            $this->apiClient->setServerParameter('HTTP_Authorization', 'Bearer '.$this->jwtManager->create($client));
        }
        $this->apiClient->request(
            $methode,
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json'
            ],
            $contentBody
        );
        return $this->apiClient->getResponse();
    }

    protected function loadClientFixture()
    {
        $this->client = new Client(
            'Jean',
            'testUsername95',
            'testmail@gmail.com',
            '0145354686',
            '123456',
            $this->encoderFactory);
        $this->entityManager->persist($this->client);
        $this->entityManager->flush();
    }

    public function createNewClient()
    {
        $client= new Client(
            'JhonDoeEntreprise',
            'JoeDoeUsername',
            'JoeDoe@gmail.com',
            '0685734986',
            'ClientBilemo0',
            $this->encoderFactory
        );
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        return $client;
    }
}