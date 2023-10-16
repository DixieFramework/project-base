<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Talav\UserBundle\Exception\UserSuspendedException;
use Twig\Environment;

class HandleExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param \Iterator<ApplicationInterface> $applications
     */
    public function __construct(
        private LoggerInterface $logger,
        private Environment $twig
    ) {
    }

    public function handleException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = null;
        $applicationCode = null;

        if ($exception instanceof AuthenticationException) {
            $this->logger->error(sprintf('Authentication error: %s', $exception->getMessage()), [
                'exception' => $exception,
            ]);

            $statusCode = 401;
		} elseif ($exception instanceof UserSuspendedException) {
            $this->logger->error('Suspended user', [
                'exception' => $exception,
            ]);

            $statusCode = 500;
        }

        if (!$statusCode) {
            return;
        }

        $response = new Response($this->twig->render('@TalavWeb/error.html.twig', [
            'exception' => $exception,
        ]), $statusCode);

        $event->setResponse($response);
        $event->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['handleException', 255],
        ];
    }
}
