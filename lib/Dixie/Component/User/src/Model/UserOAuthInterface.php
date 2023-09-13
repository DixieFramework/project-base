<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use Talav\Component\Resource\Model\ResourceInterface;

interface UserOAuthInterface extends UserAwareInterface, ResourceInterface
{
    public function getProvider(): ?string;

    public function setProvider(string $provider): void;

    public function getIdentifier(): ?string;

    public function setIdentifier(string $identifier): void;

    public function getAccessToken(): ?string;

    public function setAccessToken(?string $accessToken): void;

    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): void;
}
