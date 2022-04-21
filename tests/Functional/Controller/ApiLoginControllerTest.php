<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiLoginControllerTest extends WebTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

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
    }

    public function testApiLoginWrongCredentials(): void
    {
        $username = 'my.email@s.com';
        $password = 'Parola';
        $client = static::createClient();

        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
