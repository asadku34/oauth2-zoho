<?php

namespace Asad\OAuth2\Client\Test;

use Asad\OAuth2\Client\Provider\ZohoUser;
use PHPUnit\Framework\TestCase;

class ZohoUserTest extends TestCase
{
    public function testUserDefaults()
    {
        // Mock
        $user = new ZohoUser([
            'ZUID' => '12345',
            'Email' => 'mock.name@example.com',
            'Display_Name' => 'mock name',
            'First_Name' => 'mock',
            'Last_Name' => 'name',
        ]);

        $this->assertEquals(12345, $user->getId());
        $this->assertEquals(12345, $user->getZUID());
        $this->assertEquals('mock name', $user->getDisplayName());
        $this->assertEquals('mock', $user->getFirstName());
        $this->assertEquals('name', $user->getLastName());
        $this->assertEquals('mock.name@example.com', $user->getEmail());
    }

    public function testUserPartialData()
    {
        $user = new ZohoUser([
            'ZUID' => '12345',
            'Display_Name' => 'mock name',
            'First_Name' => 'mock',
            'Last_Name' => 'name',
        ]);

        $this->assertEquals(null, $user->getEmail());
        $this->assertEquals('mock', $user->getFirstName());
    }

    public function testUserMinimalData()
    {
        $user = new ZohoUser([
            'ZUID' => '12345',
            'Display_Name' => 'mock name',
        ]);

        $this->assertEquals(null, $user->getEmail());
        $this->assertEquals(null, $user->getFirstName());
        $this->assertEquals(null, $user->getLastName());
    }
}
