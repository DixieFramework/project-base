<?php

declare(strict_types=1);

namespace Talav\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TalavControllerListener
{
    public function onKernelController(ControllerEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST === $event->getRequestType()) {
            $controllers = $event->getController();
            if (is_array($controllers)) {
                $controller = $controllers[0];

                if (is_object($controller) && method_exists($controller, 'preExecute')
                    && $event->getRequest()->getMethod() != 'OPTIONS') {
                    $controller->preExecute($event->getRequest());
                }
            }
        }
    }
}
