<?php

declare(strict_types=1);

namespace Talav\AvatarBundle\Enum;

use Elao\Enum\Attribute\EnumCase;
use Talav\CoreBundle\Interfaces\EnumDefaultInterface;
use Talav\CoreBundle\Traits\EnumDefaultTrait;

/**
 * Avatar size enumeration.
 *
 * @implements EnumDefaultInterface<ImageSize>
 */
enum AvatarSize: string implements EnumDefaultInterface
{
    use EnumDefaultTrait;

    /*
     * The default image size used for edition (192 pixels).
     */
    #[EnumCase(extras: [EnumDefaultInterface::NAME => true])]
    case DEFAULT = 'square_large';

    /*
     * The medium image size used for user table (96 pixels).
     */
    case MEDIUM = 'square_medium';

    /*
     * The small image size used for logged user (32 pixels).
     */
    case SMALL = 'square_small';
}
