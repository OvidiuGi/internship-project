<?php

namespace App\Tests\Functional\Controller;

use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProgrammeControllerTest extends WebTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testShowProgrammes(): void
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
    }

    public function testShowProgrammesWithBadAcceptHeader(): void
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
            'HTTP_ACCEPT' => 'ceva',
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testJoinProgramme(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();
        $programmeRepository = static::getContainer()->get(ProgrammeRepository::class);
        $programme = $programmeRepository->findOneBy(['name' => 'Yoga']);
        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];

        $client->jsonRequest(
            'POST',
            'http://internship-project.local/api/programmes/join?id=' . $programme->getId(),
            [],
            [
            'HTTP_X-AUTH-TOKEN' => $token,
            ]
        );

        $client->jsonRequest(
            'POST',
            'http://internship-project.local/api/programmes/join?id=' . $programme->getId(),
            [],
            [
                'HTTP_X-AUTH-TOKEN' => $token,
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    public function testJoinNonexistentProgramme(): void
    {
        $username = 'my.email@server.com';
        $password = 'Parola';
        $client = static::createClient();
        $programmeRepository = static::getContainer()->get(ProgrammeRepository::class);
        $programme = $programmeRepository->findOneBy(['name' => '1233451']);
        $client->jsonRequest('POST', 'http://internship-project.local/api/login', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];
        $client->getRequest()->query->set('id', null);
        if (null != $programme) {
            $client->getRequest()->query->set('id', $programme->getId());
        }
        $client->jsonRequest(
            'POST',
            'http://internship-project.local/api/programmes/join',
            [],
            [
                'HTTP_X-AUTH-TOKEN' => $token,
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }
}
