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

//            $applicationCode = $exception->getApplicationCode();
        } elseif ($exception instanceof UserSuspendedException) {
            $this->logger->error('Could not retrieve users', [
                'exception' => $exception,
            ]);

//            $this->bugsnag->notifyException($exception, function ($report) {
//                $report->setSeverity('error');
//            });

//            $applicationCode = $exception->getApplicationCode();
dd($exception);
            $statusCode = 500;
        }

        if (!$statusCode) {
            return;
        }

//        if ($applicationCode) {
//            $application = $this->getApplication($applicationCode);
//
//            if ($application) {
//                $application->reset();
//            }
//        }

        $response = new Response($this->twig->render('@TalavUser/error.html.twig', [
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
//
//    private function getApplication(string $code): ?ApplicationInterface
//    {
//        foreach ($this->applications as $application) {
//            if ($application->getCode() === $code) {
//                return $application;
//            }
//        }
//
//        return null;
//    }
}
