<?php


namespace App\Tests\Controller;


use App\DTO\Phone\CreatePhone\CreatePhoneFromRequestInput;
use App\Entity\Client;
use App\Entity\Phone;
use App\Tests\AbstractWebTestCase;
use Symfony\Component\Uid\Uuid;

class PhoneControllerTest extends AbstractWebTestCase
{
    public function testListPhoneAppearWithClient()
    {
        $this->loadPhoneFixtures();
        $this->loadClientFixture();
        $response=$this->request('GET', '/api/phones', $this->client);
        $datasResponse = json_decode($response->getContent(), true);
        self::assertEquals(200, $response->getStatusCode());
        self::assertCount(20, $datasResponse);
    }

    public function testListPhoneAppearWithPages()
    {
        $this->loadPhoneFixtures();
        $this->loadClientFixture();
        $response=$this->request('GET', '/api/phones?page=0', $this->client);
        $datasResponse = json_decode($response->getContent(), true);
        self::assertEquals(200, $response->getStatusCode());
        self::assertCount(10, $datasResponse);
    }

    public function testListPhoneAppearWithBrand()
    {
        $this->loadPhoneFixtures();
        $this->loadClientFixture();
        $response=$this->request('GET', '/api/phones?brand=apple', $this->client);
        $datasResponse = json_decode($response->getContent(), true);
        self::assertEquals(200, $response->getStatusCode());
        self::assertCount(9, $datasResponse);
        self::assertStringContainsString('apple',$response->getContent());
        self::assertStringNotContainsString('samsung',$response->getContent());
    }

    public function testListPhoneAppearWithModel()
    {
        $this->loadPhoneFixtures();
        $this->loadClientFixture();
        $response=$this->request('GET', '/api/phones?model=iphone2', $this->client);
        $datasResponse = json_decode($response->getContent(), true);
        self::assertEquals(200, $response->getStatusCode());
        self::assertCount(1, $datasResponse);
        self::assertStringContainsString('iphone2',$response->getContent());

    }

    public function testListPhoneNoClient()
    {
        $response=$this->request('GET', '/api/phones');
        self::assertEquals(401, $response->getStatusCode());
        self::assertStringContainsString('JWT Token not found',$response->getContent());
    }

    public function testOnePhoneShownWithClient()
    {
        $phone = new Phone(
            'iphone',
            '5S',
            50.10,
            'apple',
            2.5,
            16,
            'blue',
            'fantastic phone'
        );
        $this->entityManager->persist($phone);
        $this->entityManager->flush();
        $response = $this->request('GET', '/api/phones/' . $phone->getId(),$this->client);
        self::assertEquals(200,$response->getStatusCode());
    }

    public function testOnePhoneShownNoClient()
    {
        $phone = $this->createPhone();
        $response = $this->request('GET', '/api/phones/'.$phone->getId());
        self::assertEquals(401,$response->getStatusCode());
        self::assertStringContainsString('JWT Token not found',$response->getContent());
    }

    public function testOnePhoneWrongId()
    {
        $response = $this->request('GET', '/api/phones/'.Uuid::v4()->__toString(),$this->client);
        self::assertEquals(404,$response->getStatusCode());
        self::assertStringContainsString('not found',$response->getContent());
    }

    public function testCreatePhoneGoodDatas()
    {
        Client::makeAdmin($this->client);
        $response=$this->request(
            'POST',
            '/api/phones',
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": "blue",
              "description": "The best phone" 
            }');
        self::assertEquals(201,$response->getStatusCode());
    }

    public function testCreatePhoneWithWrongDatas()
    {
        Client::makeAdmin($this->client);
        $response=$this->request(
            'POST',
            '/api/phones',
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": 7,
              "description": "The best phone" 
            }');
        self::assertEquals(500,$response->getStatusCode());
    }

    public function testCreatePhoneWithNotAdmin()
    {
        $response=$this->request(
            'POST',
            '/api/phones',
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": "green",
              "description": "The best phone" 
            }');
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('Forbidden',$response->getContent());
    }

    public function testUpdatePhoneWithGooData()
    {
        Client::makeAdmin($this->client);
        $phone = $this->createPhone();
        $response=$this->request(
            'PUT',
            '/api/phones/'.$phone->getId(),
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": "green",
              "description": "The best phone" 
            }');
        self::assertEquals(204,$response->getStatusCode());
        self::assertSame($phone->getColor(),'green');
    }

    public function testUpdatePhoneWithWrongLink()
    {
        Client::makeAdmin($this->client);
        $phone = $this->createPhone();

        $response=$this->request(
            'PUT',
            '/api/phones/'.Uuid::v4()->__toString(),
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": "green",
              "description": "The best phone" 
            }');
        self::assertEquals(404,$response->getStatusCode());
        self::assertNotSame($phone->getColor(),'green');

    }

    public function testUpdatePhoneNotAdmin()
    {
        $phone = $this->createPhone();
        $response=$this->request(
            'PUT',
            '/api/phones/'.$phone->getId(),
            $this->client,
            '{
              "brand": "apple",
              "password": "iphone",
              "model": "5s",
              "price": 50.64,
              "system": "apple",
              "screenSize": 4.00,
              "storage": 8,
              "color": "green",
              "description": "The best phone" 
            }');
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('Forbidden',$response->getContent());
        self::assertNotSame($phone->getColor(),'green');

    }

    public function testDeletePhoneWithAdmin()
    {
        Client::makeAdmin($this->client);
        $phone = $this->createPhone();
        $response = $this->request('DELETE', '/api/phones/' . $phone->getId(),$this->client);
        self::assertEquals(204,$response->getStatusCode());
    }

    public function testDeletePhoneWithNotAdmin()
    {
        $phone = $this->createPhone();
        $response = $this->request('DELETE', '/api/phones/' . $phone->getId(),$this->client);
        self::assertEquals(403,$response->getStatusCode());
        self::assertStringContainsString('unauthorized', $response->getContent());
    }


    public function createPhone()
    {
        $phone = new Phone(
            'iphone',
            '5S',
            50.10,
            'apple',
            2.5,
            16,
            'blue',
            'fantastic phone'
        );
        $this->entityManager->persist($phone);
        $this->entityManager->flush();
        return $phone;
    }

    protected function loadPhoneFixtures(){

        for ($i=0; $i < 20; $i++) {
            $phoneDTO = new CreatePhoneFromRequestInput();
            $phoneDTO->description = $this->faker->text(500);
            $phoneDTO->screenSize = $this->faker->randomFloat(2, 1.00, 7.00);
            if ($i<=10) {
                $phoneDTO->system = "android";
                $phoneDTO->model = "galaxy S" . ($i + 1);
                $phoneDTO->brand = "samsung";
            }
            elseif( $i>10){
                $phoneDTO->system = "iOS";
                $phoneDTO->model = "iphone" . ($i - 10 + 1);
                $phoneDTO->brand = "apple";
            }
            else{
                $phoneDTO->system = "android";
                $phoneDTO->model = "P" . ($i + 1);
                $phoneDTO->brand = "Huawei";
            }

            $phoneDTO->price = $this->faker->randomFloat(2, 10.00, 500.00);
            $phoneDTO->color = $this->faker->safeColorName;
            $phoneDTO->storage = 8 * $this->faker->numberBetween(1, 16);
            $phone = Phone::createFromRequest($phoneDTO);

            $this->entityManager->persist($phone);
        }
        $this->entityManager->flush();
    }




    
    
}