<?php

declare(strict_types=1);

namespace Talav\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $allowedOrigins;


    /**
     * Constructor.
     *
     * @param string|null $allowedOrigins
     */
    public function __construct(string $allowedOrigins = '*')
    {
        $this->allowedOrigins = $allowedOrigins;
    }

    /**
     * Kernel response event handler.
     *
     * @param ResponseEvent $event
     */
    public function onResponse(ResponseEvent $event): void
    {
        if (empty($this->allowedOrigins)) {
            return;
        }

        $response = $event->getResponse();

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            return;
        }

        $response->headers->set('Access-Control-Allow-Origin', $this->allowedOrigins);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }
}
