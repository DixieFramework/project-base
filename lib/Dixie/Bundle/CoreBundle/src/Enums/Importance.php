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
 * The notification email importance enumeration.
 *
 * @implements EnumDefaultInterface<Importance>
 * @implements EnumSortableInterface<Importance>
 */
#[ReadableEnum(prefix: 'importance.', useValueAsDefault: true)]
enum Importance: string implements EnumDefaultInterface, EnumSortableInterface, EnumTranslatableInterface
{
    use EnumDefaultTrait;
    use EnumTranslatableTrait;

    /*
     * High importance.
     */
    case HIGH = 'high';

    /*
     * Low importance (default value).
     */
    #[EnumCase(extras: [EnumDefaultInterface::NAME => true])]
    case LOW = 'low';

    /*
     * Medium  importance.
     */
    case MEDIUM = 'medium';

    /*
     * Urgente importance.
     */
    case URGENT = 'urgent';

    /**
     * Gets the full human representation of the value (to be translated).
     */
    public function getReadableFull(): string
    {
        return $this->getReadable() . '_full';
    }

    /**
     * @return Importance[]
     */
    public static function sorted(): array
    {
        return [
            self::LOW,
            self::MEDIUM,
            self::HIGH,
            self::URGENT,
        ];
    }
}
