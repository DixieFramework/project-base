<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Utils\StringUtils;

/**
 * Trait to get an exception context.
 */
trait ExceptionContextTrait
{
    /**
     * Gets the context, as array; for the given exception.
     *
     * @param \Throwable $e the exception to get the context for
     *
     * @psalm-return array{
     *     message: string,
     *     code: string|int,
     *     file: string,
     *     line: int,
     *     class: string,
     *     trace: string}
     */
    public function getExceptionContext(\Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'class' => StringUtils::getShortName($e),
            'trace' => $e->getTraceAsString(),
        ];
    }
}
