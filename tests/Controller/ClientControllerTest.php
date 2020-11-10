<?php


namespace App\Tests\Controller;


use App\Entity\Client;
use App\Tests\AbstractWebTestCase;
use Symfony\Component\Uid\Uuid;

class ClientControllerTest extends AbstractWebTestCase
{
    public function testCreateClientGoodDatas()
    {
        Client::makeAdmin($this->client);
        $response= $this->request(
            'POST',
            '/api/clients',
            $this->client,
        '{
            "username": "JoeDoeUsername",
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(201,$response->getStatusCode());
    }

    public function testCreateClientWithNotAdmin()
    {
        $response= $this->request(
            'POST',
            '/api/clients',
            $this->client,
            '{
            "username": "JoeDoeUsername",
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('Forbidden',$response->getContent());
    }

    public function testCreateClientWithWrongUsername()
    {
        Client::makeAdmin($this->client);
        $response= $this->request(
            'POST',
            '/api/clients',
            $this->client,
            '{
            "username": 55,
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(500,$response->getStatusCode());
    }

    public function testCreateClientWithMissingData()
    {
        Client::makeAdmin($this->client);
        $response= $this->request(
            'POST',
            '/api/clients',
            $this->client,
            '{
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(400,$response->getStatusCode());
    }

    public function testUpdateClientGoodData()
    {
        Client::makeAdmin($this->client);
        $client=$this->createNewClient();
        $response= $this->request(
            'PUT',
            '/api/clients/'.$client->getId(),
            $this->client,
            '{
            "username": "newUsername",
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "createdAt": 150,
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(204,$response->getStatusCode());
        self::assertSame($client->getUsername(),'newUsername');
    }

    public function testUpdateClientWrongUsername()
    {
        Client::makeAdmin($this->client);
        $client=$this->createNewClient();
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        $response= $this->request(
            'PUT',
            '/api/clients/'.$client->getId(),
            $this->client,
            '{
            "username": 55,
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(500,$response->getStatusCode());
    }

    public function testUpdateClientNotAdmin()
    {
        $client=$this->createNewClient();
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        $response= $this->request(
            'PUT',
            '/api/clients/'.$client->getId(),
            $this->client,
            '{
            "username": "newUserName",
            "name": "JoeDoeUsername",
            "mail": "JoeDoe@gmail.com",
            "phoneNumber": "0685734986",
            "password": "ClientBilemo0"
            }'
        );
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('Forbidden',$response->getContent());
    }

    public function testListClientAppearForAdmin()
    {
        Client::makeAdmin($this->client);
        $response= $this->request('GET', '/api/clients', $this->client);
        $responseJson = json_decode($response->getContent(), true);
        self::assertEquals(200,$response->getStatusCode());
        self::assertCount(1,$responseJson);
    }

    public function testListClientAppearForNotAdmin()
    {
        $response= $this->request('GET', '/api/clients', $this->client);
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('Forbidden',$response->getContent());
    }

    public function testListClientNoClient()
    {
        $response= $this->request('GET', '/api/clients');
        self::assertEquals(401, $response->getStatusCode());
        self::assertStringContainsString('JWT Token not found',$response->getContent());
    }

    public function testOneClientShownWithAdmin()
    {
        Client::makeAdmin($this->client);
        $newClient = $this->createNewClient();
        $response = $this->request('GET', '/api/clients/' . $newClient->getId(),$this->client);
        self::assertEquals(200,$response->getStatusCode());
        self::assertStringContainsString('JhonDoeEntreprise',$response->getContent());
    }

    public function testOneCLientShownWithHisCount()
    {
        $newClient = $this->createNewClient();
        $response = $this->request('GET', '/api/clients/' . $newClient->getId(), $newClient);
        self::assertEquals(200,$response->getStatusCode());
        self::assertStringContainsString('JhonDoeEntreprise',$response->getContent());
    }

    public function testOneClientWrongId()
    {
        Client::makeAdmin($this->client);
        $response = $this->request('GET', '/api/clients/' . Uuid::v4()->__toString(),$this->client);
        self::assertEquals(404,$response->getStatusCode());
        self::assertStringContainsString('not found',$response->getContent());
    }

    public function testDeleteClientWithAdmin()
    {
        Client::makeAdmin($this->client);
        $client = $this->createNewClient();
        $response = $this->request('DELETE', '/api/clients/' . $client->getId(),$this->client);
        self::assertEquals(204,$response->getStatusCode());
    }

    public function testDeleteClientWithNotAdmin()
    {
        $client = $this->createNewClient();
        $response = $this->request('DELETE', '/api/clients/' . $client->getId(),$this->client);
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('unauthorized', $response->getContent());
    }
}