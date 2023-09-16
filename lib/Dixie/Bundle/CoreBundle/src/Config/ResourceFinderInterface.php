<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Config;

use Symfony\Component\Finder\Finder;

interface ResourceFinderInterface
{
    /**
     * Returns a Finder object with the resource paths set.
     *
     * @return Finder
     */
    public function find(): Finder;

    /**
     * Appends the subpath to the resource paths and returns a Finder object.
     *
     * @param string $subpath
     *
     * @return Finder
     */
    public function findIn(string $subpath): Finder;
}
