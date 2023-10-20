<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig;

use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Service\ThemeService;
use Symfony\Component\HttpFoundation\Request;
use Talav\WebBundle\Event\ThemeEvent;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for theme functions.
 */
class ThemeExtension extends AbstractExtension
{
    public function __construct(private readonly ThemeService $service, private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('themes', fn () => $this->service->getThemes()),
            new TwigFunction('theme', fn (Request $request) => $this->service->getTheme($request)),
            new TwigFunction('theme_value', fn (Request $request) => $this->service->getThemeValue($request)),
            new TwigFunction('is_dark_theme', fn (Request $request) => $this->service->isDarkTheme($request)),
        ];
    }

    /**
     * @param Environment $environment
     * @param string $eventName
     * @param mixed|null $payload
     * @return ThemeEvent
     */
    public function trigger(Environment $environment, string $eventName, $payload = null): ThemeEvent
    {
        /** @var AppVariable $app */
        $app = $environment->getGlobals()['app'];
        /** @var UserInterface $user */
        $user = $app->getUser();

        $themeEvent = new ThemeEvent($user, $payload);

        if ($this->eventDispatcher->hasListeners($eventName)) {
            $this->eventDispatcher->dispatch($themeEvent, $eventName);
        }

        return $themeEvent;
    }
}
