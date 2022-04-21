<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsletterControllerTest extends WebTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testSendNewsletterToOneAction(): void
    {
        $telephoneNr = '123145';
        $body = 'Test message';
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

        $client->jsonRequest('POST', 'http://internship-project.local/api/newsletter', [
            'receiver' => $telephoneNr,
            'body' => $body,
        ], [
            'HTTP_X-AUTH-TOKEN' => $token,
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testSendNewletterToAllAction(): void
    {
        $body = 'Test message';
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

        $client->jsonRequest('POST', 'http://internship-project.local/api/newsletter/all', [
            'body' => $body,
        ], [
            'HTTP_X-AUTH-TOKEN' => $token,
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testSendNewsletterToNonexistentUser(): void
    {
        $telephoneNr = '11111';
        $body = 'Test message';
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

        $client->jsonRequest('POST', 'http://internship-project.local/api/newsletter', [
            'receiver' => $telephoneNr,
            'body' => $body,
        ], [
            'HTTP_X-AUTH-TOKEN' => $token,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
