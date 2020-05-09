# ZOHO Provider for OAuth 2.0 Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asad/oauth2-zoho.svg?style=flat-square)](https://packagist.org/packages/asad/oauth2-zoho)
[![Build Status](https://img.shields.io/travis/asadku34/oauth2-zoho/master.svg?style=flat-square)](https://travis-ci.org/asadku34/oauth2-zoho)
[![Quality Score](https://img.shields.io/scrutinizer/g/asadku34/oauth2-zoho.svg?style=flat-square)](https://scrutinizer-ci.com/g/asadku34/oauth2-zoho)
[![Total Downloads](https://img.shields.io/packagist/dt/asad/oauth2-zoho.svg?style=flat-square)](https://packagist.org/packages/asad/oauth2-zoho)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/asad/zoho-cliq)

This package provides [ZOHO OAuth 2.0][oauth-setup] support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

To use this package, it will be necessary to have a ZOHO client ID and client
secret. These are referred to as `{zoho-client-id}` and `{zoho-client-secret}`
in the documentation.

Please follow the [ZOHO instructions][oauth-setup] to create the required credentials.

[oauth-setup]: https://www.zoho.com/crm/developer/docs/api/oauth-overview.html

## Installation

You can install the package via composer:

```bash
composer require asad/oauth2-zoho
```

## Usage

### Authorization Code Flow

```php
use Asad\OAuth2\Client\Provider\Zoho;

$provider = new Zoho([
    'clientId' => '{zoho-client-id}',
    'clientSecret' => '{zoho-client-secret}',
    'redirectUri' => 'http://localhost:8000/zoho/oauth2',
    'dc' => 'AU' //It will be optional if your ZOHO are in US location
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => [
            'ZohoCRM.modules.ALL', //Important: Define your data accessability scope here
            'ZohoCRM.settings.ALL',
        ],
        'access_type' => 'offline' //Important: If you want to generate the refresh token, set this value as offline
    ]);

    $_SESSION['oauth2state'] = $provider->state;
    header('Location: ' . $authUrl);
    exit;

    // Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {

    // Try to get an access token (using the authorization code grant)
    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        //$user = $provider->getResourceOwner($token);


        echo $access_token = $token->getToken();

        echo $refresh_token = $token->getRefreshToken(); //Save this refresh token to somewehre

        echo $token->getExpires();

    } catch (\Exception $e) {
        //handle you exception
    }
}
```

## Refreshing a Token

Refresh tokens are only provided to applications which request offline access. You can specify offline access by passing the access_type option in your getAuthorizationUrl() request.

```php
use Asad\OAuth2\Client\Provider\Zoho;
use League\OAuth2\Client\Grant\RefreshToken;

$provider = new Zoho([
    'clientId' => '{zoho-client-id}',
    'clientSecret' => '{zoho-client-secret}',
    'dc' => 'AU' //It will be optional if your ZOHO are in US location
]);

$refreshToken = 'FromYourStoredData';
$grant = new RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);

```

### Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email asadku34@gamil.com instead of using the issue tracker.

## Credits

-   [Asadur Rahman](https://github.com/asadku34)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
