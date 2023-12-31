<?php

declare(strict_types=1);

namespace Talav\Component\User\ValueObject;

use Webmozart\Assert\Assert;

class Username implements \Stringable
{
    private const MIN_LENGTH = 4;
    private const MAX_LENGTH = 30;
    private const FORMAT = '/^(\w(?:(?:\w|(?:\.(?!\.))){4,30}(?:\w))?)$/';

    private readonly string $username;

    private function __construct(string $username)
    {
        Assert::notEmpty($username, 'authentication.validations.empty_username');
        Assert::minLength($username, self::MIN_LENGTH, 'authentication.validations.short_username');
        Assert::maxLength($username, self::MAX_LENGTH, 'authentication.validations.long_username');
//        Assert::regex($username, self::FORMAT, 'authentication.validations.invalid_username_pattern');

        $this->username = $username;
    }

    public function __toString()
    {
        return $this->username;
    }

    public static function fromString(string $username): self
    {
        return new self($username);
    }

    public function equals(string|self $username): bool
    {
        if ($username instanceof self) {
            return $this->username === $username->username;
        }

        return $this->username === $username;
    }
}
