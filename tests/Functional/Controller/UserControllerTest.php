<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testRemoveUser(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $client->jsonRequest('POST', 'http://internship-project.local/api', [
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
        $client->request('DELETE', 'http://internship-project.local/api/users/' . $testUser->getId(), [], [], [
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

        $client->jsonRequest('POST', 'http://internship-project.local/api', [
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
        $client->request('DELETE', 'http://internship-project.local/api/users/' . $testUser->getId(), [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(302);
        $client->jsonRequest('PATCH', 'http://internship-project.local/api/users', [
            'email' => $testUser->email,
        ]);
        $this->assertResponseStatusCodeSame(302);
    }
}
