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
 * The password strength level.
 *
 * @implements EnumDefaultInterface<StrengthLevel>
 * @implements EnumSortableInterface<StrengthLevel>
 */
#[ReadableEnum(prefix: 'strength_level.')]
enum StrengthLevel: int implements EnumDefaultInterface, EnumSortableInterface, EnumTranslatableInterface
{
    use EnumDefaultTrait;
    use EnumTranslatableTrait;

    /*
     * Medium level.
     */
    #[EnumCase('medium')]
    case MEDIUM = 2;

    /*
     * No validation level (default value).
     */
    #[EnumCase('none', [EnumDefaultInterface::NAME => true])]
    case NONE = -1;

    /*
     * Strong level.
     */
    #[EnumCase('strong')]
    case STRONG = 3;

    /*
     * Very strong level.
     */
    #[EnumCase('very_strong')]
    case VERY_STRONG = 4;

    /*
     * Very weak level.
     */
    #[EnumCase('very_weak')]
    case VERY_WEAK = 0;

    /*
     * Weak level.
     */
    #[EnumCase('weak')]
    case WEAK = 1;

    /**
     * Returns if this value is smaller than the given level.
     */
    public function isSmaller(int|StrengthLevel $level): bool
    {
        if ($level instanceof self) {
            $level = $level->value;
        }

        return $this->value < $level;
    }

    /**
     * Returns the percentage of this level.
     *
     * @return int a value between 0 and  100
     */
    public function percent(): int
    {
        return \max(0, ($this->value + 1) * 20);
    }

    /**
     * @return StrengthLevel[]
     */
    public static function sorted(): array
    {
        return [
            self::NONE,
            self::VERY_WEAK,
            self::WEAK,
            self::MEDIUM,
            self::STRONG,
            self::VERY_STRONG,
        ];
    }

    /**
     * Gets the strength level values.
     *
     * @return int[]
     */
    public static function values(): array
    {
        return \array_map(static fn (self $level): int => $level->value, self::sorted());
    }
}
