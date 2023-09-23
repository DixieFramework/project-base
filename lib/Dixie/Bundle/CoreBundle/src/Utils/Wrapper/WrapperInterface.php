<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Utils\Wrapper;

/**
 * Interface WrapperInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface WrapperInterface
{
    /**
     * Get wrapped value.
     *
     * @param  mixed  $src
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __invoke(mixed $src): mixed;
}
