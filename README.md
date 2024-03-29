# EVE Online Provider for OAuth 2.0 Client

[![GitHub Source](https://img.shields.io/badge/source-killmails%2Foauth2--eve-blue?style=flat-square)](https://github.com/killmails/oauth2-eve)
[![GitHub Release](https://img.shields.io/github/v/release/killmails/oauth2-eve?style=flat-square)](https://github.com/killmails/oauth2-eve/releases)
[![GitHub License](https://img.shields.io/github/license/killmails/oauth2-eve?style=flat-square)](LICENSE.md)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/killmails/oauth2-eve/ci.yaml?style=flat-square)](https://github.com/killmails/oauth2-eve/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/killmails/oauth2-eve?style=flat-square)](https://packagist.org/packages/killmails/oauth2-eve)

This package provides EVE Online OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```sh
composer require killmails/oauth2-eve:^2.0
```

## Usage

Usage is the same as The League's OAuth client, using `\Killmails\OAuth2\Client\Provider\EveOnline` as the provider.

### Authorization Code Flow

```php
use Killmails\OAuth2\Client\Provider\EveOnline;

$sso = new EveOnline([
    'clientId' => '{eveonline-client-id}',
    'clientSecret' => '{eveonline-client-secret}',
    'redirectUri' => 'https://example.com/callback-url',
]);

if (empty($_GET['code'])) {
    $url = $sso->getAuthorizationUrl();
    $_SESSION['state'] = $sso->getState();

    header('Location: '.$authUrl);
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {
    unset($_SESSION['state']);
} else {
    $token = $sso->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    $user = $sso->getResourceOwner($token);

    printf('Hello, %s (%d)', $user->getName(), $user->getId());
}
```

### Managing Scopes

When creating your EVE Online authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'scope' => ['esi-killmails.read_killmails.v1']
];

$url = $sso->getAuthorizationUrl($options);

// ...
```

If neither are defined, the provider will utilize internal defaults.

## Testing

``` sh
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
