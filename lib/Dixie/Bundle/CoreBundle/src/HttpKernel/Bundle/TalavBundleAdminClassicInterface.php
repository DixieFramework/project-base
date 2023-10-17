<?php

declare(strict_types=1);

namespace Talav\CoreBundle\HttpKernel\Bundle;

use Talav\CoreBundle\Routing\RouteReferenceInterface;

interface TalavBundleAdminClassicInterface
{
    /**
     * Get javascripts to include in admin interface
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getJsPaths(): array;

    /**
     * Get stylesheets to include in admin interface
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getCssPaths(): array;

    /**
     * Get javascripts to include in editmode
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getEditmodeJsPaths(): array;

    /**
     * Get stylesheets to include in editmode
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getEditmodeCssPaths(): array;
}
