<?php

declare(strict_types=1);

namespace Talav\Component\User\Canonicalizer;

use Talav\Component\User\ValueObject\Username;

final class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize(Username|string $username): ?string
    {
        $string = match (true) {
            $username instanceof Username => $username->__toString(),
            default => $username
        };

        return null === $string ? null : mb_convert_case($string, \MB_CASE_LOWER, mb_detect_encoding($string));
    }
}
