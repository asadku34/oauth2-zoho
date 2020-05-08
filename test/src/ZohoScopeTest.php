<?php

namespace Asad\OAuth2\Client\Test;

use Asad\OAuth2\Client\Provider\Zoho;
use PHPUnit\Framework\TestCase;

class ZohoScopeTest extends TestCase
{
    public function testDefaultScopes()
    {
        $provider = new Zoho([
            'clientId' => 'client-id',
            'clientSecret' => 'client-secret',
        ]);

        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('scope', $query);
        $this->assertSame('aaaserver.profile.READ', $query['scope']);
    }

    public function testOptionScopes()
    {
        $provider = new Zoho([
            'clientId' => 'client-id',
            'clientSecret' => 'client-secret',
        ]);

        $url = $provider->getAuthorizationUrl([
            'access_type' => 'mock_access_type',
            'scope' => ['moc_user', 'moc_settings', 'moc_read'],
        ]);
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('scope', $query);
        $this->assertEquals('mock_access_type', $query['access_type']);

        $this->assertContains('moc_user', $query['scope']);
        $this->assertContains('moc_settings', $query['scope']);
        $this->assertContains('moc_read', $query['scope']);
    }
}
