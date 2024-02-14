<?php

namespace Killmails\OAuth2\Client\Provider;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class EveOnline extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public function getBaseAuthorizationUrl(): string
    {
        return 'https://login.eveonline.com/v2/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://login.eveonline.com/v2/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://login.eveonline.com/oauth/jwks';
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        // no-op
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $jwt = JWT::decode($token, JWK::parseKeySet($response));

        return new EveOnlineResourceOwner([
            'sub' => $jwt->sub,
            'name' => $jwt->name,
            'owner' => $jwt->owner,
        ]);
    }
}
