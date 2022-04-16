<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRemoveUser(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];
        $usernameResponse = $decodedContent['user'];

//        $client->request('GET', 'http://internship-project.local/api/programmes', [], [], [
//            'HTTP_X-AUTH-TOKEN' => $token,
//            'HTTP_ACCEPT' => 'application/json',
//        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($username, $usernameResponse);
        $testUser = $userRepository->findOneBy(['email' => $username]);
        $client->request('DELETE', 'http://internship-project.local/api/users/delete/' . $testUser->getId(), [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(302);
    }

    public function testRecoverUser(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];
        $usernameResponse = $decodedContent['user'];

        $this->assertResponseIsSuccessful();
        $this->assertEquals($username, $usernameResponse);

        $testUser = $userRepository->findOneBy(['email' => $username]);
        $client->request('DELETE', 'http://internship-project.local/api/users/delete/' . $testUser->getId(), [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(302);
        $client->jsonRequest('POST', 'http://internship-project.local/api/users/recover', [
            'email' => $testUser->email,
        ]);
        $this->assertResponseStatusCodeSame(302);
    }

    public function testShowAllUsers(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();

        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];

        $client->request('GET', 'http://internship-project.local/api/users', [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
}
