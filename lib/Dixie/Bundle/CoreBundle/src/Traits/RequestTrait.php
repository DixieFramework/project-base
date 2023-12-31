<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait to get value from a request.
 */
trait RequestTrait
{
    /**
     * Returns all the request parameters.
     */
    protected function getRequestAll(Request $request, string $key, array $default = []): array
    {
        return $this->getRequestBag($request, $key)?->all($key) ?? $default;
    }

    /**
     * Returns the request parameter value converted to boolean.
     */
    protected function getRequestBoolean(Request $request, string $key, bool $default = false): bool
    {
        return $this->getRequestBag($request, $key)?->getBoolean($key, $default) ?? $default;
    }

    /**
     * Returns the parameter value converted to an enum.
     *
     * @psalm-template E of \BackedEnum
     *
     * @psalm-param class-string<E> $class
     * @psalm-param E|null          $default
     *
     * @psalm-return E|null
     */
    protected function getRequestEnum(Request $request, string $key, string $class, \BackedEnum $default = null): ?\BackedEnum
    {
        $value = $this->getRequestBag($request, $key)?->getEnum($key, $class, $default);

        return $value ?? $default;
    }

    /**
     * Returns the request parameter value converted to float.
     */
    protected function getRequestFloat(Request $request, string $key, float $default = 0): float
    {
        return (float) ($this->getRequestBag($request, $key)?->get($key, $default) ?? $default);
    }

    /**
     * Returns the request parameter value converted to integer.
     */
    protected function getRequestInt(Request $request, string $key, int $default = 0): int
    {
        return $this->getRequestBag($request, $key)?->getInt($key, $default) ?? $default;
    }

    /**
     * Returns the request parameter value converted to string.
     *
     * @psalm-return ($default is null ? (string|null) : string)
     */
    protected function getRequestString(Request $request, string $key, string $default = null): ?string
    {
        return $this->getRequestBag($request, $key)?->getString($key, $default ?? '') ?? $default;
    }

    /**
     * Returns the request parameter value.
     *
     * @psalm-return ($default is null ? (string|int|float|bool|null) : string|int|float|bool)
     */
    protected function getRequestValue(Request $request, string $key, string|int|float|bool $default = null): string|int|float|bool|null
    {
        /** @psalm-var scalar $value */
        $value = $this->getRequestBag($request, $key)?->get($key, $default) ?? $default;

        return $value;
    }

    private function getRequestBag(Request $request, string $key): ?ParameterBag
    {
        if ($request->attributes->has($key)) {
            return $request->attributes;
        }
        if ($request->query->has($key)) {
            return $request->query;
        }
        if ($request->request->has($key)) {
            return $request->request;
        }

        return null;
    }
}
