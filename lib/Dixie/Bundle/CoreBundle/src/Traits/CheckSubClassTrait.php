<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

/**
 * Trait to check subclass parameter.
 */
trait CheckSubClassTrait
{
    /**
     * Check if the given source is a class or a subclass of the given target class name.
     *
     * @throws \InvalidArgumentException if check failed
     *
     * @psalm-param class-string $target
     */
    public function checkSubClass(string|object $source, string $target): void
    {
        if (!\is_a($source, $target, true) && !\is_subclass_of($source, $target)) {
            throw new \InvalidArgumentException(\sprintf('Expected argument of type "%s", "%s" given.', $target, \get_debug_type($source)));
        }
    }
}
