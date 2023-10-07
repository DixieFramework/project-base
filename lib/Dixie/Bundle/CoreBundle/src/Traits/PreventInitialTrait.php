<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

trait PreventInitialTrait
{
    /**
     * Prevent implement class.
     */
    protected function __construct()
    {
        //
    }
}
