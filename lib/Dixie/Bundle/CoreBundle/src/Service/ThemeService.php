<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Enums\Theme;
use Talav\CoreBundle\Traits\CookieTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service to manage user theme.
 */
class ThemeService
{
    use CookieTrait;

    /**
     * The key name for selected theme cookie.
     */
    private const KEY_THEME = 'THEME';

    /**
     * Gets the selected theme from cookies.
     */
    public function getTheme(Request $request): Theme
    {
        return $this->getCookieEnum(request: $request, key: self::KEY_THEME, class: Theme::class, default: Theme::getDefault());
    }

    /**
     * Gets the sorted themes.
     *
     * @return Theme[]
     */
    public function getThemes(): array
    {
        return Theme::sorted();
    }

    /**
     * Returns the selected theme value.
     */
    public function getThemeValue(Request $request): string
    {
        return $this->getTheme($request)->value;
    }

    /**
     * Returns if the dark theme is selected.
     */
    public function isDarkTheme(Request $request): bool
    {
        return Theme::DARK === $this->getTheme($request);
    }

    /**
     * Save the given theme to cookies.
     */
    public function saveTheme(Response $response, string $path, Theme $theme): void
    {
        $this->setCookie(response: $response, key: self::KEY_THEME, value: $theme, path: $path, httpOnly: false);
    }
}
