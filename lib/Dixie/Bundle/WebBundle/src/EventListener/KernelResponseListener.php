<?php

declare(strict_types=1);

namespace Talav\WebBundle\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Talav\WebBundle\Engine\AssetFilter;

final class KernelResponseListener implements EventSubscriberInterface
{
    public function __construct(private readonly AssetFilter $assetFilter)
    {
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $this->filterResponse($event->getRequest(), $event->getResponse());
    }

    /**
     * Filter the Response to add page assets and vars and return.
     */
    protected function filterResponse(Request $request, Response $response)
    {
        $jsAssets = [];
        $cssAssets = [];
        $filteredContent = $this->assetFilter->filter($response->getContent(), $jsAssets, $cssAssets);
        $response->setContent($filteredContent);

        return $response;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => ['onKernelResponse', -255],
        ];
    }
}
