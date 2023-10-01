<?php

declare(strict_types = 1);

namespace Groshy\Entity;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Card
{
    #[Assert\NotBlank]
    private string $holder;

    #[Assert\NotBlank]
    #[Assert\CardScheme(['VISA', 'MASTERCARD', 'MAESTRO'])]
    private int $number;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Length(3)]
    private int $securityCode;

    private DateTimeImmutable $expireAt;

    public function getHolder(): string
    {
        return $this->holder;
    }

    public function setHolder(string $holder): void
    {
        $this->holder = $holder;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getSecurityCode(): int
    {
        return $this->securityCode;
    }

    public function setSecurityCode(int $securityCode): void
    {
        $this->securityCode = $securityCode;
    }

    public function getExpireAt(): DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(DateTimeImmutable $expireAt): void
    {
        $this->expireAt = $expireAt;
    }
}
