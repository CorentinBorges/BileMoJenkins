<?php

namespace App\DataFixtures;

use App\DTO\Client\CreateClient\CreateClientFromRequestInput;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ClientFixtures extends Fixture
{
    /**
     * @var Generator $faker
     */
    protected $faker;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }


    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $clientDTO = new CreateClientFromRequestInput();
            $clientDTO->name = $this->faker->company;
            $clientDTO->password = 'ClientBilemo0';
            $clientDTO->mail = $this->faker->email;
            $clientDTO->phoneNumber = $this->faker->phoneNumber;
            $clientDTO->username = $this->faker->userName;

            $client = Client::createClientFromFixtures($clientDTO, $this->encoderFactory);

            $this->setReference(Client::class . '_' . $i, $client);
            $manager->persist($client);
        }

        $clientAdmin = new CreateClientFromRequestInput();
        $clientAdmin->username = "Admin";
        $clientAdmin->password = "AdminBilemo0";
        $clientAdmin->name = "BileMo";
        $clientAdmin->mail = "bilemo@gmail.com";
        $clientAdmin->phoneNumber = $this->faker->phoneNumber;

        $clientAdmin = Client::createClientFromFixtures($clientAdmin, $this->encoderFactory, ["ROLE_ADMIN"]);
        $manager->persist($clientAdmin);
        $manager->flush();
    }
}
