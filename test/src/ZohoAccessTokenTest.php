<?php

namespace Asad\OAuth2\Client\Test;

use Asad\OAuth2\Client\AccessToken\ZohoAccessToken;
use PHPUnit\Framework\TestCase;

class ZohoAccessTokenTest extends TestCase
{
    public function testZohoAccessToken()
    {
        $live = 3600;
        $param = [
            'access_token' => 'moc_token',
            'refresh_token' => 'moc_refresh_token',
            'api_domain' => 'moc_domain_name',
            'token_type' => 'moc_bearer',
            'expires_in' => $live,
        ];

        $access_token = new ZohoAccessToken($param);
        $this->assertInstanceOf('League\OAuth2\Client\Token\AccessToken', $access_token);

        $this->assertEquals('moc_token', $access_token->getToken());
        $this->assertEquals('moc_refresh_token', $access_token->getRefreshToken());
        $this->assertEquals('moc_domain_name', $access_token->getApiDomain());
        $this->assertEquals('moc_bearer', $access_token->getTokenType());
        $this->assertEquals($live + time(), $access_token->getExpires());
    }
}
