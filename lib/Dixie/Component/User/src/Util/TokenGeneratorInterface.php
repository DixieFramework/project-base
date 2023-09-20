<?php

declare(strict_types=1);

namespace Talav\Component\User\Util;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
}
