<?php

declare(strict_types=1);

namespace Talav\Component\User\Canonicalizer;

use Talav\Component\User\ValueObject\Username;

interface CanonicalizerInterface
{
    public function canonicalize(Username|string $username): ?string;
}
