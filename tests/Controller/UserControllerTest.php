<?php


namespace App\Tests\Controller;


use App\Entity\Client;
use App\Entity\User;
use App\Tests\AbstractWebTestCase;

class UserControllerTest extends AbstractWebTestCase
{
    public function testUserListWithGoodClient()
    {
        for ($i=1; $i<4; $i++) {
            $this->createNewUser($this->client, $i);
        }
        $response = $this->request('GET', "/api/clients/".$this->client->getId()."/users", $this->client);
        $datasJson = json_decode($response->getContent(), true);
        self::assertEquals(200,$response->getStatusCode());
        self::assertCount(3,$datasJson);
    }

    public function testUserListWithAdmin()
    {
        $newClient = $this->createNewClient();
        Client::makeAdmin($this->client);
        for ($i=1; $i<4; $i++) {
            $this->createNewUser($newClient, $i);
        }
        $response = $this->request('GET', "/api/clients/".$newClient->getId()."/users", $this->client);
        $datasJson = json_decode($response->getContent(), true);
        self::assertEquals(200,$response->getStatusCode());
        self::assertCount(3,$datasJson);
    }

    //todo:ask for header change
//    public function testUserListWithBadClient()
//    {
//        for ($i=1; $i<4; $i++) {
//            $this->createNewUser($this->client, $i);
//        }
//        $newClient = $this->createNewClient();
//        $response = $this->request('GET', "/api/clients/".$newClient->getId()."/users", $this->client);
//    }

    public function testUserDetailsWithGoodClient()
    {
        $user=$this->createNewUser($this->client);
        $response = $this->request(
            'GET',
            "/api/clients/".$this->client->getId()."/users/".
            $user->getId(),
            $this->client);
        self::assertEquals(200,$response->getStatusCode());
        self::assertStringContainsString('JJLeTest98',$response->getContent());
    }

    public function testCreateUserWithGoodData()
    {
        $response=$this->request(
            'POST',
            '/api/clients/'.$this->client->getId()."/users",
            $this->client,
            '{
                "fullName": "Jean LeTest",
                "username": "JJLeTest98",
                "email": "JJ@gmail.com"
            }'
        );
        self::assertEquals(201,$response->getStatusCode());
    }

    public function testCreateUserWithNoFullName()
    {
        $response=$this->request(
            'POST',
            '/api/clients/'.$this->client->getId()."/users",
            $this->client,
            '{
                "username": "JJLeTest98",
                "email": "JJ@gmail.com"
            }'
        );
        self::assertEquals(400,$response->getStatusCode());
    }

    public function testUpdateUserWithGoodData()
    {
        $user = $this->createNewUser($this->client);
        $response=$this->request(
            'PUT',
            '/api/clients/'.$this->client->getId()."/users/".$user->getId(),
            $this->client,
            '{
                "fullName": "NewFullName",
                "username": "JJLeTest98",
                "email": "JJ@gmail.com"
            }'
        );
        self::assertEquals(204,$response->getStatusCode());
        self::assertSame($user->getFullName(),'NewFullName');
    }

    public function testUpdateUserWithMissingData()
    {
        $user = $this->createNewUser($this->client);
        $response=$this->request(
            'PUT',
            '/api/clients/'.$this->client->getId()."/users/".$user->getId(),
            $this->client,
            '{
                "username": "JJLeTest98",
                "email": "JJ@gmail.com"
            }'
        );
        self::assertEquals(400,$response->getStatusCode());
    }

    public function testDeleteUserWithGoodClient()
    {
        $user = $this->createNewUser($this->client);
        $response = $this->request(
            'DELETE',
            "/api/clients/" . $this->client->getId() . "/users/" . $user->getId(),
            $this->client);
        self::assertEquals(204,$response->getStatusCode());
    }

    public function testDeleteUserWithoutClient()
    {
        $user = $this->createNewUser($this->client);
        $response = $this->request(
            'DELETE',
            "/api/clients/" . $this->client->getId() . "/users/" . $user->getId());
        self::assertEquals(401,$response->getStatusCode());
    }


//    public function testUserDetailsWithWrongClient()
//    {
//        $newClient = $this->createNewClient();
//        $user=$this->createNewUser($newClient);
//
//        $response = $this->request(
//            'GET',
//            "/api/clients/".$newClient->getId()."/users/".
//            $user->getId(),
//            $this->client);
//        self::assertEquals(200,$response->getStatusCode());
//        self::assertStringContainsString('JJLeTest98',$response->getContent());
//    }



    public function createNewUser($client,int $indice=null)
    {
        $user = new User(
            'Jean LeTest'. $indice,
            'JJLeTest98'. $indice,
            'JJ' . $indice . '@gmail.com',
            $client
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}