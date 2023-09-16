<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig;

use Talav\CoreBundle\Service\ThemeService;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for theme functions.
 */
class ThemeExtension extends AbstractExtension
{
    public function __construct(private readonly ThemeService $service)
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
}
