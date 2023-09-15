<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

use Talav\CoreBundle\Interfaces\EnumDefaultInterface;
use Talav\CoreBundle\Traits\EnumDefaultTrait;
use Elao\Enum\Attribute\EnumCase;

/**
 * Image size enumeration.
 *
 * @implements EnumDefaultInterface<ImageSize>
 */
enum ImageSize: int implements EnumDefaultInterface
{
    use EnumDefaultTrait;

    /*
     * The default image size used for edition (192 pixels).
     */
    #[EnumCase(extras: [EnumDefaultInterface::NAME => true])]
    case DEFAULT = 192;

    /*
     * The medium image size used for user table (96 pixels).
     */
    case MEDIUM = 96;

    /*
     * The small image size used for logged user (32 pixels).
     */
    case SMALL = 32;
}
