<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

use Talav\CoreBundle\Interfaces\EnumTranslatableInterface;
use Talav\CoreBundle\Traits\EnumTranslatableTrait;
use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\ExtrasTrait;

/**
 * Flash bag type enumeration.
 */
#[ReadableEnum(prefix: 'flash_bag.', useValueAsDefault: true)]
enum FlashType: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;
    use ExtrasTrait;

    /*
     * Danger flash bag.
     */
    case DANGER = 'danger';

    /*
     * Information  flash bag.
     */
    case INFO = 'info';

    /*
     * Success flash bag.
     */
    case SUCCESS = 'success';

    /*
     * Warning flash bag.
     */
    case WARNING = 'warning';
}
