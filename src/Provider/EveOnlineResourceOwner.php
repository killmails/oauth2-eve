<?php

namespace Killmails\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EveOnlineResourceOwner implements ResourceOwnerInterface
{
    /**
     * @param array<string,mixed> $response
     */
    public function __construct(protected readonly array $response = []) {}

    public function getId(): int
    {
        return (int) substr($this->response["sub"], 14);
    }

    public function getName(): string
    {
        return $this->response["name"];
    }

    public function getOwner(): string
    {
        return $this->response["owner"];
    }

    public function toArray(): array
    {
        return $this->response;
    }
}
