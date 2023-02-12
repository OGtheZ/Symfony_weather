<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiRegisterTest extends WebTestCase
{
    public function testRegistration(): void
    {
        static::createClient()->request('POST', '/api/register',
            [
                'email' => 'tester@test.com',
                'password' => 'password',
            ]
        );
        $this->assertResponseIsSuccessful();
    }
}
