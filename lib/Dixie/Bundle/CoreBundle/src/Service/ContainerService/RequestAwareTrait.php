<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service\ContainerService;

use Talav\CoreBundle\Traits\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RequestAwareTrait
 * @deprecated
 * @package Druidvav\EssentialsBundle\Service\ContainerService
 */
trait RequestAwareTrait
{
    use ContainerAwareTrait;

    /**
     * Shortcut to return the request service.
     * @deprecated
     */
    public function getRequest(): Request
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
