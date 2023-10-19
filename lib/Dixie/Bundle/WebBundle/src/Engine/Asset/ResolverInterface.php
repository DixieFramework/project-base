<?php

declare(strict_types=1);

namespace Talav\WebBundle\Engine\Asset;

use Talav\WebBundle\Engine\AssetBag;

/**
 * Interface ResolverInterface
 *
 * Provide an interface for Resolver classes.
 */
interface ResolverInterface
{
    public function compile(): string;

    public function getBag(): AssetBag;
}
