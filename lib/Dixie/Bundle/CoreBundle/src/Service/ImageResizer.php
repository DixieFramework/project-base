<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Enums\ImageExtension;
use Talav\CoreBundle\Enums\ImageSize;
use Talav\CoreBundle\Traits\ImageSizeTrait;
use Talav\CoreBundle\Traits\LoggerAwareTrait;
use Talav\CoreBundle\Traits\TranslatorAwareTrait;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Service to resize images.
 */
class ImageResizer implements ServiceSubscriberInterface
{
    use ImageSizeTrait;
    use LoggerAwareTrait;
    use ServiceSubscriberTrait;
    use TranslatorAwareTrait;

    private ?ImagineInterface $imagine = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        try {
            $this->imagine = new Imagine();
        } catch (\Exception $e) {
            $this->logException($e, $this->trans('user.image.failure'));
        }
    }

    /**
     * Resize an image with the given size.
     *
     * @param string    $source the source image path
     * @param string    $target the target image path
     * @param ImageSize $size   the image size
     *
     * @return bool true on success, false on error or if the size is not a positive value
     */
    public function resize(string $source, string $target, ImageSize $size): bool
    {
        if (!$this->imagine instanceof ImagineInterface) {
            return false;
        }

        try {
            $options = ['format' => ImageExtension::PNG->value];
            $newSize = $this->getNewSize($source, $size->value);
            $this->imagine->open($source)
                ->resize($newSize)
                ->save($target, $options);

            return true;
        } catch (\Exception $e) {
            $this->logException($e, $this->trans('user.image.failure'));

            return false;
        }
    }

    /**
     * Resize the given image with the default size (192 pixels).
     *
     * @param string $source the source image path
     * @param string $target the target image path
     *
     * @return bool true on success, false on error
     */
    public function resizeDefault(string $source, string $target): bool
    {
        return $this->resize($source, $target, ImageSize::DEFAULT);
    }

    /**
     * Resize the given image with the medium size (96 pixels).
     *
     * @param string $source the source image path
     * @param string $target the target image path
     *
     * @return bool true on success, false on error
     */
    public function resizeMedium(string $source, string $target): bool
    {
        return $this->resize($source, $target, ImageSize::MEDIUM);
    }

    /**
     * Resize the given image with the small size (32 pixels).
     *
     * @param string $source the source image path
     * @param string $target the target image path
     *
     * @return bool true on success, false on error
     */
    public function resizeSmall(string $source, string $target): bool
    {
        return $this->resize($source, $target, ImageSize::SMALL);
    }

    private function getNewSize(string $filename, float $size): Box
    {
        [$imageWidth, $imageHeight] = $this->getImageSize($filename);
        $ratio = (float) $imageWidth / (float) $imageHeight;
        $width = $size;
        $height = $size;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        return new Box((int) $width, (int) $height);
    }
}
