<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

use DateTimeImmutable;
use DateTimeInterface;

trait TimestampableTrait
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    public function createDateTime(DateTimeInterface|string|null $date): ?DateTimeImmutable
    {
        if (is_string($date)) {
            $datetime = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date);

            return false === $datetime ? null : $datetime;
        } elseif ($date instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($date);
        }

        return null;
    }
}
