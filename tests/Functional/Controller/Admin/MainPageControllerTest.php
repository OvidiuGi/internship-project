<?php

namespace App\Tests\Functional\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainPageControllerTest extends WebTestCase
{
    public function testInputExists(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://internship-project.local/admin');
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }
}