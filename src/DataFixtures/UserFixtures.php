<?php

namespace App\DataFixtures;

use App\DTO\User\CreateUser\CreateUserFromFixture;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    /**
     * @var Generator $faker
     */
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 30; $i++) {
            $userDTO = new CreateUserFromFixture();
            $userDTO->email = $this->faker->email;
            $userDTO->username = $this->faker->userName;
            $userDTO->client = $this->getReference(Client::class . '_' . $this->faker->numberBetween(0, 9));
            $userDTO->fullName = $this->faker->name;

            $user = User::createFromFixture($userDTO);

            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * Load firsts fixtures
     *
     * @return string[]
     */
    public function getDependencies()
    {
        return [ClientFixtures::class];
    }
}
