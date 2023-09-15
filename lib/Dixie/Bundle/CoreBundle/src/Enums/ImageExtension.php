<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

use Talav\CoreBundle\Interfaces\EnumDefaultInterface;
use Talav\CoreBundle\Traits\EnumDefaultTrait;
use Elao\Enum\Attribute\EnumCase;

/**
 * Image file extension numeration.
 *
 * @implements EnumDefaultInterface<ImageExtension>
 */
enum ImageExtension: string implements EnumDefaultInterface
{
    use EnumDefaultTrait;

    /*
     * The Bitmap file extension ("bmp").
     */
    case BMP = 'bmp';

    /*
     * The Gif file extension ("gif").
     */
    case GIF = 'gif';

    /*
     * The JPEG file extension ("jpeg").
     */
    case JPEG = 'jpeg';

    /*
     * The JPG file extension ("jpg").
     */
    case JPG = 'jpg';

    /*
     * The PNG file extension ("png").
     */
    #[EnumCase(extras: [EnumDefaultInterface::NAME => true])]
    case PNG = 'png';

    /*
     * The XBM file extension ("xbm").
     */
    case XBM = 'xbm';
}
