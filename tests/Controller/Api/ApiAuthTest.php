<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiAuthTest extends WebTestCase
{
    public function testApiAuth(): void
    {
        $client = static::createClient();


        $client->request('POST', '/api/register',
            [
                'email' => 'tester@test.com',
                'password' => 'password',
            ]
        );

        $client->request(
        'POST',
        '/api/auth',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'email' => 'tester@test.com',
            'password' => 'password',
        ])
        );

        $this->assertResponseIsSuccessful();
    }
}
