<?php

declare(strict_types=1);

namespace Talav\Bundle\CoreBundle\Utils;

use Symfony\Component\DependencyInjection\ServiceSubscriberInterface as LegacyServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

if (interface_exists(ServiceSubscriberInterface::class)) {
    abstract class AbstractServiceSubscriber implements ServiceSubscriberInterface
    {
    }
} elseif (interface_exists(LegacyServiceSubscriberInterface::class)) {
    abstract class AbstractServiceSubscriber implements LegacyServiceSubscriberInterface
    {
    }
}
