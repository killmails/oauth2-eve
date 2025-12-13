<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Killmails\OAuth2\Client\Provider\EveOnline;
use Killmails\OAuth2\Client\Provider\EveOnlineResourceOwner;
use League\OAuth2\Client\Token\AccessToken;

function provider(): EveOnline
{
    return new EveOnline([
        "clientId" => "mock_client_id",
        "clientSecret" => "mock_client_secret",
        "redirectUri" => "mock_redirect",
    ]);
}

function resourceOwner(int|string $id = 123456): EveOnlineResourceOwner
{
    return new EveOnlineResourceOwner([
        "sub" => "CHARACTER:EVE:" . $id,
        "name" => "mock_name",
        "owner" => "mock_owner",
    ]);
}

function accessToken(): AccessToken
{
    return new AccessToken([
        "access_token" => "mock_access_token",
        "expires" => time() + 5 * 60,
        "refresh_token" => "mock_refresh_token",
    ]);
}

function httpClient(array $responses): Client
{
    $headers = ["content-type" => "application/json; charset=utf-8"];

    return new Client([
        "handler" => HandlerStack::create(
            new MockHandler(
                array_map(
                    fn(mixed $body): Response => new Response(
                        body: json_encode($body),
                        headers: $headers,
                    ),
                    $responses,
                ),
            ),
        ),
    ]);
}

function keys(): array
{
    $private =
        "-----BEGIN PRIVATE KEY-----\nMIIBVAIBADANBgkqhkiG9w0BAQEFAASCAT4wggE6AgEAAkEAptKlljYdQ0xHpY8h\nZRERvT5IuAsdmtoGcswMUXqLPA4IhY/fSj6SSvpW5LRTzoT4jTIvNQrVOzHulW92\nxOx4DwIDAQABAkA8MLiqD/BS/czEAXaNHrGF1FksfmY6HvxAozq2kz51dg2YvTBi\nfi0Z/c/7Ybn+FUaLd+V5ZsgGa157rQPh3QCxAiEA0ua5Ec+ZfTyvJ6qbFIdMfzjR\nzXRh8mfnNCv0cZNfwjMCIQDKfvUw3lPuqexpLabGrLb74yvdIye2UOflOtMLGGEu\ntQIgQZmUnU6mnobesIbnn/YJvFTPJYn64WyiRN8bNKyzj3MCIQCLyqeEDAgPXBlL\n5usafsN4ErUGXa2drC7azghBwZvdfQIgQEp2P5r9LBwHnKIXA7O2zXpQHJRAfu0S\ndG+TOfcW7gQ=\n-----END PRIVATE KEY-----";

    $public =
        "-----BEGIN PUBLIC KEY-----\nMFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKbSpZY2HUNMR6WPIWUREb0+SLgLHZra\nBnLMDFF6izwOCIWP30o+kkr6VuS0U86E+I0yLzUK1Tsx7pVvdsTseA8CAwEAAQ==\n-----END PUBLIC KEY-----";

    return [$private, $public];
}
