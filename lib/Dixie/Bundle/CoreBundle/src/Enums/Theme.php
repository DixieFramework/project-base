<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

use Talav\CoreBundle\Interfaces\EnumDefaultInterface;
use Talav\CoreBundle\Interfaces\EnumSortableInterface;
use Talav\CoreBundle\Interfaces\EnumTranslatableInterface;
use Talav\CoreBundle\Traits\EnumDefaultTrait;
use Talav\CoreBundle\Traits\EnumTranslatableTrait;
use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Attribute\ReadableEnum;

/**
 * Theme style enumeration.
 *
 * @implements EnumDefaultInterface<Theme>
 * @implements EnumSortableInterface<Theme>
 */
#[ReadableEnum(prefix: 'theme.', suffix: '.name', useValueAsDefault: true)]
enum Theme: string implements EnumDefaultInterface, EnumSortableInterface, EnumTranslatableInterface
{
    use EnumDefaultTrait;
    use EnumTranslatableTrait;

    /*
    * The auto (system) theme.
    */
    #[EnumCase(extras: ['icon' => 'fa-solid fa-circle-half-stroke', EnumDefaultInterface::NAME => true])]
    case AUTO = 'auto';

    /*
     * The dark theme.
     */
    #[EnumCase(extras: ['icon' => 'fa-solid fa-moon', 'light-icon' => 'fa-regular fa-moon'])]
    case DARK = 'dark';

    /*
     * The light theme.
     */
    #[EnumCase(extras: ['icon' => 'fa-solid fa-sun', 'light-icon' => 'fa-regular fa-sun'])]
    case LIGHT = 'light';

    /**
     * Gets the help message (to be translated).
     */
    public function getHelp(): string
    {
        return \sprintf('theme.%s.help', $this->value);
    }

    /**
     * Gets the icon.
     */
    public function getIcon(): string
    {
        return $this->getExtraString('icon');
    }

    /**
     * Gets the icon for dark mode.
     */
    public function getIconDark(): string
    {
        return $this->getIcon();
    }

    /**
     * Gets the icon for light mode.
     */
    public function getIconLight(): string
    {
        return $this->getExtraString('light-icon', $this->getIcon());
    }

    /**
     * Gets the success message (to be translated).
     */
    public function getSuccess(): string
    {
        return \sprintf('theme.%s.success', $this->value);
    }

    /**
     * Gets the title (to be translated).
     */
    public function getTitle(): string
    {
        return \sprintf('theme.%s.title', $this->value);
    }

    /**
     * @return Theme[]
     */
    public static function sorted(): array
    {
        return [
            self::LIGHT,
            self::DARK,
            self::AUTO,
        ];
    }
}
