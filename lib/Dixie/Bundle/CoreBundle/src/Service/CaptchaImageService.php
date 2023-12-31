<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Traits\SessionAwareTrait;
use Talav\CoreBundle\Utils\StringUtils;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Service to generate and validate a captcha image.
 */
class CaptchaImageService implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    use SessionAwareTrait;

    /**
     * The allowed characters.
     */
    private const ALLOWED_VALUES = 'abcdefghjklmnpqrstuvwxyz23456789';

    /**
     * The space between characters.
     */
    private const CHAR_SPACE = 3;

    /**
     * The base 64 image data prefix.
     */
    private const IMAGE_PREFIX = 'data:image/png;base64,';

    /**
     * The attribute name for the encoded image data.
     */
    private const KEY_DATA = 'captcha_data';

    /**
     * The attribute name for the captcha text.
     */
    private const KEY_TEXT = 'captcha_text';

    /**
     * The attribute name for the captcha timeout.
     */
    private const KEY_TIME = 'captcha_time';

    /**
     * The maximum validation timeout in seconds (3 minutes).
     */
    private const MAX_TIME_OUT = 180;

    /**
     * Constructor.
     */
    public function __construct(
        #[Autowire('%kernel.project_dir%/resources/fonts/captcha.ttf')]
        private readonly string $font
    ) {
    }

    /**
     * Remove captcha values from the session.
     */
    public function clear(): self
    {
        $this->removeSessionValues(self::KEY_TEXT, self::KEY_TIME, self::KEY_DATA);

        return $this;
    }

    /**
     * Generate a captcha image and save values to the session.
     *
     * @param bool $force  true to recreate an image, false to take the previous created image (if any)
     * @param int  $length the number of characters to output
     * @param int  $width  the image width
     * @param int  $height the image height
     *
     * @return string|null the image encoded with the base 64 or null if the image canot be created
     *
     * @throws \Exception
     */
    public function generateImage(bool $force = false, int $length = 6, int $width = 150, int $height = 30): ?string
    {
        if (!$force && $this->validateTimeout() && $this->hasSessionValue(self::KEY_DATA)) {
            return $this->getSessionString(self::KEY_DATA);
        }
        $this->clear();
        $text = $this->generateRandomString($length);
        if (($image = $this->createImage($text, $width, $height)) instanceof ImageService) {
            $data = $this->encodeImage($image);
            $this->setSessionValues([
                self::KEY_TEXT => $text,
                self::KEY_DATA => $data,
                self::KEY_TIME => \time(),
            ]);

            return $data;
        }

        return null;
    }

    /**
     * Validate the timeout.
     */
    public function validateTimeout(): bool
    {
        $actual = \time();
        $last = $this->getSessionInt(self::KEY_TIME, 0);
        $delta = $actual - $last;

        return $delta <= self::MAX_TIME_OUT;
    }

    /**
     * Validate the given token; ignoring case.
     */
    public function validateToken(?string $token): bool
    {
        return null !== $token && StringUtils::equalIgnoreCase($token, $this->getSessionString(self::KEY_TEXT, ''));
    }

    /**
     * Compute the text layout.
     *
     * @pslam-return array<array{angle: int(-8, 8), char: string, height: int, width: int}>
     *
     * @throws \Exception
     */
    private function computeText(ImageService $image, float $size, string $font, string $text): array
    {
        return \array_map(function (string $char) use ($image, $size, $font): array {
            $angle = \random_int(-8, 8);
            [$width, $height] = $image->ttfSize($size, $angle, $font, $char);

            return [
                'char' => $char,
                'angle' => $angle,
                'width' => $width,
                'height' => $height,
            ];
        }, \str_split($text));
    }

    /**
     * Create an image.
     *
     * @throws \Exception
     */
    private function createImage(string $text, int $width, int $height): ?ImageService
    {
        if (!($image = ImageService::fromTrueColor($width, $height)) instanceof ImageService) {
            return null;
        }
        $this->drawBackground($image)
            ->drawPoints($image, $width, $height)
            ->drawLines($image, $width, $height)
            ->drawText($image, $width, $height, $text);

        return $image;
    }

    /**
     * Draws the white background image.
     */
    private function drawBackground(ImageService $image): self
    {
        $color = $image->allocateWhite();
        if (\is_int($color)) {
            $image->fill($color);
        }

        return $this;
    }

    /**
     * Draws horizontal gray lines in the background.
     *
     * @throws \Exception
     */
    private function drawLines(ImageService $image, int $width, int $height): self
    {
        $color = $image->allocate(195, 195, 195);
        if (\is_int($color)) {
            $lines = \random_int(3, 7);
            for ($i = 0; $i < $lines; ++$i) {
                $y1 = \random_int(0, $height);
                $y2 = \random_int(0, $height);
                $image->line(0, $y1, $width, $y2, $color);
            }
        }

        return $this;
    }

    /**
     * Draws blue points in the background.
     *
     * @throws \Exception
     */
    private function drawPoints(ImageService $image, int $width, int $height): self
    {
        $color = $image->allocate(0, 0, 255);
        if (\is_int($color)) {
            $points = \random_int(300, 400);
            for ($i = 0; $i < $points; ++$i) {
                $x = \random_int(0, $width);
                $y = \random_int(0, $height);
                $image->setPixel($x, $y, $color);
            }
        }

        return $this;
    }

    /**
     * Draws the image text.
     *
     * @throws \Exception
     */
    private function drawText(ImageService $image, int $width, int $height, string $text): self
    {
        $font = $this->font;
        $color = $image->allocateBlack();
        if (\is_int($color)) {
            $size = (int) ((float) $height * 0.7);
            /** @psalm-var non-empty-array<array{angle: int, char: string, height: int, width: int}> $items */
            $items = $this->computeText($image, $size, $font, $text);
            $textHeight = \max(\array_column($items, 'height'));
            $textWidth = \array_sum(\array_column($items, 'width')) + (\count($items) - 1) * self::CHAR_SPACE;
            $x = \intdiv($width - $textWidth, 2);
            $y = \intdiv($height - $textHeight, 2) + $size;
            foreach ($items as $item) {
                $image->ttfText($size, $item['angle'], $x, $y, $color, $font, $item['char']);
                $x += $item['width'] + self::CHAR_SPACE;
            }
        }

        return $this;
    }

    /**
     * Encodes the image with MIME base64.
     */
    private function encodeImage(ImageService $image): string
    {
        \ob_start();
        $image->toPng();
        $buffer = (string) \ob_get_contents();
        \ob_end_clean();

        return self::IMAGE_PREFIX . \base64_encode($buffer);
    }

    /**
     * Generate a random string.
     */
    private function generateRandomString(int $length): string
    {
        $length = \min(\max($length, 2), \strlen(self::ALLOWED_VALUES));
        $result = \str_shuffle(self::ALLOWED_VALUES);

        return \substr($result, 0, $length);
    }
}
