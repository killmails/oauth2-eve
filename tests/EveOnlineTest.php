<?php

use Firebase\JWT\JWT;
use Killmails\OAuth2\Client\Provider\EveOnlineResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Strobotti\JWK\KeyFactory;

test('get authorization url', function (): void {
    $url = provider()->getAuthorizationUrl();

    expect(parse_url($url, PHP_URL_SCHEME))->toBe('https');
    expect(parse_url($url, PHP_URL_HOST))->toBe('login.eveonline.com');
    expect(parse_url($url, PHP_URL_PATH))->toBe('/v2/oauth/authorize');
    expect(parse_url($url, PHP_URL_QUERY))->not->toBeNull();

    parse_str(parse_url($url, PHP_URL_QUERY), $query);

    expect($query)->toHaveKeys([
        'state',
        'scope',
        'response_type',
        'redirect_uri',
        'client_id',
    ]);

    expect($query['state'])->not->toBeEmpty();
    expect($query['scope'])->toBeEmpty();

    expect($query['response_type'])->toBe('code');
    expect($query['redirect_uri'])->toBe('mock_redirect');
    expect($query['client_id'])->toBe('mock_client_id');
});

test('get access token url', function (): void {
    $url = provider()->getBaseAccessTokenUrl([]);

    expect(parse_url($url, PHP_URL_SCHEME))->toBe('https');
    expect(parse_url($url, PHP_URL_HOST))->toBe('login.eveonline.com');
    expect(parse_url($url, PHP_URL_PATH))->toBe('/v2/oauth/token');
    expect(parse_url($url, PHP_URL_QUERY))->toBeNull();
});

test('get resource owner details url', function (): void {
    $url = provider()->getResourceOwnerDetailsUrl(accessToken());

    expect(parse_url($url, PHP_URL_SCHEME))->toBe('https');
    expect(parse_url($url, PHP_URL_HOST))->toBe('login.eveonline.com');
    expect(parse_url($url, PHP_URL_PATH))->toBe('/oauth/jwks');
    expect(parse_url($url, PHP_URL_QUERY))->toBeNull();
});

test('get access token', function (): void {
    $jwt = accessToken();

    $token = provider()
        ->setHttpClient(httpClient([$jwt]))
        ->getAccessToken('authorization_code', ['code' => 'mock_code']);

    expect($token)->toBeInstanceOf(AccessToken::class);

    expect($token->getToken())->toBe($jwt->getToken());
    expect($token->getExpires())->toBe($jwt->getExpires());
    expect($token->getRefreshToken())->toBe($jwt->getRefreshToken());
});

test('get resource owner details', function (): void {
    [$private, $public] = keys();

    $provider = provider();
    $owner = resourceOwner();

    $payload = $owner->toArray() + [
        'exp' => time() + 5 * 60,
        'iat' => time() - 10,
    ];

    $alg = 'RS256';
    $kid = 'JWT-Signature-Key';

    $jwt = [
        'access_token' => JWT::encode($payload, $private, $alg, $kid),
    ];

    $jwks = ['keys' => [
        (new KeyFactory)->createFromPem($public, [
            'use' => 'sig',
            'alg' => $alg,
            'kid' => $kid,
        ]),
    ]];

    $provider->setHttpClient(httpClient([$jwt, $jwks]));

    $token = $provider->getAccessToken('authorization_code', ['code' => 'mock_code']);
    $user = $provider->getResourceOwner($token);

    expect($user)->toBeInstanceOf(EveOnlineResourceOwner::class);

    expect($user->getId())->toBe($owner->getId());
    expect($user->getName())->toBe($owner->getName());
    expect($user->getOwner())->toBe($owner->getOwner());
});
