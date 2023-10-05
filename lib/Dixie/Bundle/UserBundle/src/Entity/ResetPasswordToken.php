<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\Component\User\Model\UserAwareTrait;

class ResetPasswordToken
{
    use UserAwareTrait;
    use TimestampableTrait;

    private mixed $id;

    private ?string $token;

    public function __construct()
    {
        try {
            $this->token = substr(
                string: bin2hex(random_bytes(max(1, intval(ceil(60 / 2))))),
                offset: 0,
                length: 60
            );
        } catch (\Exception) {
            $this->token = null;
        }
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function isExpired(int $interval): bool
    {
        try {
            $expirationDate = new \DateTime('-' . $interval . ' minutes');

            return $this->getCreatedAt() < $expirationDate;
        } catch (\Exception) {
            return false;
        }
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token = null): self
    {
        $this->token = $token;

        return $this;
    }
}
