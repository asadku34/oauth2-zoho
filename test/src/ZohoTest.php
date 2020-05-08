<?php

namespace Asad\OAuth2\Client\Test;

use Eloquent\Phony\Phpunit\Phony;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Asad\OAuth2\Client\Provider\Zoho;
use Asad\OAuth2\Client\AccessToken\ZohoAccessToken;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ZohoTest extends TestCase
{
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Zoho([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('approval_prompt', $query);

        $this->assertAttributeNotEmpty('state', $this->provider);
    }

    public function testBaseAccessTokenUrl()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('/oauth/v2/token', $uri['path']);
        $this->assertEquals('accounts.zoho.com', $uri['host']);
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = $this->mockAccessToken();

        $url = $this->provider->getResourceOwnerDetailsUrl($token);

        $this->assertEquals('https://accounts.zoho.com/oauth/user/info', $url);
    }

    public function testUserData()
    {
        // Mock
        $response = [
            'ZUID' => '12345',
            'Email' => 'mock.name@example.com',
            'Display_Name' => 'mock name',
            'First_Name' => 'mock',
            'Last_Name' => 'name',
        ];

        $token = $this->mockAccessToken();

        $provider = Phony::partialMock(Zoho::class);
        $provider->fetchResourceOwnerDetails->returns($response);
        $zoho = $provider->get();

        // Execute
        $user = $zoho->getResourceOwner($token);

        // Verify
        Phony::inOrder(
            $provider->fetchResourceOwnerDetails->called()
        );

        $this->assertInstanceOf('League\OAuth2\Client\Provider\ResourceOwnerInterface', $user);

        $this->assertEquals(12345, $user->getId());
        $this->assertEquals('mock name', $user->getDisplayName());
        $this->assertEquals('mock', $user->getFirstName());
        $this->assertEquals('name', $user->getLastName());
        $this->assertEquals('mock.name@example.com', $user->getEmail());

        $user = $user->toArray();

        $this->assertArrayHasKey('ZUID', $user);
        $this->assertArrayHasKey('Display_Name', $user);
        $this->assertArrayHasKey('First_Name', $user);
        $this->assertArrayHasKey('Last_Name', $user);
        $this->assertArrayHasKey('Email', $user);
    }

    public function testErrorResponse()
    {
        // Mock
        $error_json = '{"error": "invalid_code"}';

        $response = Phony::mock('GuzzleHttp\Psr7\Response');
        $response->getHeader->returns(['application/json']);
        $response->getBody->returns($error_json);

        $provider = Phony::partialMock(Zoho::class);
        $provider->getResponse->returns($response);

        $zoho = $provider->get();

        $token = $this->mockAccessToken();

        // Expect
        $this->expectException(IdentityProviderException::class);

        // Execute
        $user = $zoho->getResourceOwner($token);

        // Verify
        Phony::inOrder(
            $provider->getResponse->calledWith($this->instanceOf('GuzzleHttp\Psr7\Request')),
            $response->getHeader->called(),
            $response->getBody->called()
        );
    }

    public function testCreateAccessToken()
    {
        $live_time = 3600;
        $response_json = [
            'access_token' => 'moc_access_token',
            'refresh_token' => 'moc_refresh_token',
            'api_domain' => 'moc_domain',
            'token_type' => 'Bearer',
            'expires_in' => $live_time,
        ];
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($response_json));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);
        /**
         * @var ZohoAccessToken $token
         *
         * */
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals($response_json['access_token'], $token->getToken());
        $this->assertEquals(time() + $response_json['expires_in'], $token->getExpires());
        $this->assertEquals($response_json['refresh_token'], $token->getRefreshToken());
        $this->assertEquals($response_json['api_domain'], $token->getApiDomain());
        $this->assertEquals($response_json['token_type'], $token->getTokenType());
    }

    /**
     * @return ZohoAccessToken
     */
    private function mockAccessToken()
    {
        return new ZohoAccessToken([
            'access_token' => 'mock_access_token',
        ]);
    }
}
