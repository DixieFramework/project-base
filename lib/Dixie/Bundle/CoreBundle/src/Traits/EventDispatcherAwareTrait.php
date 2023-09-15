<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EventDispatcherAwareTrait
{
    protected EventDispatcherInterface $dispatcher;

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }
}
