<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * Extends logger trait wih the subscribed service.
 *
 * @property \Psr\Container\ContainerInterface $container
 */
trait LoggerAwareTrait
{
    use LoggerTrait;

    private ?LoggerInterface $logger = null;

    #[SubscribedService]
    public function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            /* @noinspection PhpUnhandledExceptionInspection */
            $this->logger = $this->container->get(self::class . '::' . __FUNCTION__);
        }

        return $this->logger;
    }

    public function setLogger(?LoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }
}
