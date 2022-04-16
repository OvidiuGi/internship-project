<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiLoginControllerTest extends WebTestCase
{
    public function testApiLogin(): void
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
        $usernameResponse = $decodedContent['user'];

        $client->request('GET', 'http://internship-project.local/api/programmes', [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($username, $usernameResponse);

        $client->request('DELETE', 'http://internship-project.local/api/users/delete/1', [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
    }
}
