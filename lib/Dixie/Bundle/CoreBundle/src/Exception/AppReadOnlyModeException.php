<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Exception;

final class AppReadOnlyModeException extends TalavException
{
    public const MESSAGE = 'app_read_only_mode';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
