<?php

declare(strict_types=1);

namespace Talav\MediaBundle\Twig\Extension;

use Groshy\Kernel;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Talav\AvatarBundle\Enum\AvatarSize;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Service\NonceService;
use Talav\CoreBundle\Traits\ImageSizeTrait;
use Talav\CoreBundle\Utils\FileUtils;
use Talav\CoreBundle\Utils\StringUtils;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use function Talav\value;

final class MediaRuntime implements RuntimeExtensionInterface
{
    use ImageSizeTrait;

    /**
     * The default file version.
     */
    private int $version = Kernel::VERSION_ID;

    /**
     * The asset versions.
     *
     * @var array<string, int>
     */
    private array $versions = [];

    /**
     * The public directory.
     */
    private readonly string $webDir;

    public function __construct(
        #[Autowire('%kernel.project_dir%/composer.lock')]
        string $composer_file,
        #[Autowire('%kernel.debug%')]
        private readonly bool $debug,
        #[Autowire('%kernel.project_dir%/public')]
        string $webDir,
        #[Autowire(service: 'twig.extension.assets')]
        private readonly AssetExtension $extension,
        private readonly NonceService $service,
        private readonly ProviderPool $pool,
        private readonly Environment $twig,
    ) {
        if (FileUtils::exists($composer_file)) {
            $version = \filemtime($composer_file);
            if (\is_int($version)) {
                $this->version = $version;
            }
        }

        $this->webDir = FileUtils::normalize($webDir);
    }

    public function mediaReference(MediaInterface $media): string
    {
        $provider = $this->pool->getProvider($media->getProviderName());

        return $provider->getPublicUrl($media);
    }

    public function thumbReference(MediaInterface $media, string $formatName): string
    {
        $provider = $this->pool->getProvider($media->getProviderName());

        return $provider->getThumbnailPublicUrl($media, $formatName);
    }

    public function thumb(MediaInterface $media, string $formatName, ?iterable $options = []): string
    {
        $provider = $this->pool->getProvider($media->getProviderName());
        $template = $provider->getTemplateConfig()->getThumb();

        $options = array_merge($provider->getViewHelperProperties($media, $formatName, $options), $options);

        return $this->twig->render($template, [
            'media' => $media,
            'options' => $options,
        ]);
    }

    public function media($media, string $formatName, ?iterable $options = []): string
    {
        $provider = $this->pool->getProvider($media->getProviderName());
        $template = $provider->getTemplateConfig()->getView();
        $options = $provider->getViewHelperProperties($media, $formatName, $options);

        return $this->twig->render($template, [
            'media' => $media,
            'format' => $formatName,
            'options' => $options,
        ]);
    }

    // ---

    /**
     * Checks if the given asset path exists.
     */
    private function assetExists(?string $path): bool
    {
        $file = $this->getRealPath($path);
        if (null === $file) {
            return false;
        }

        return StringUtils::startWith($file, $this->webDir);
    }

    /**
     * Output an image tag with a version.
     *
     * @param array<string, string|int> $parameters
     */
    private function assetImage(string $path, array $parameters = [], string $packageName = null): string
    {
        [$width, $height] = $this->imageSize($path);
        $parameters = \array_merge([
            'src' => $this->versionedAsset($path, $packageName),
            'height' => $height,
            'width' => $width,
        ], $parameters);
        $attributes = $this->reduceParams($parameters);

        return "<image $attributes>";
    }

    /**
     * Output the user image profile.
     *
     * @psalm-param array<string, string|int> $parameters
     */
    public function assetImageUser(?UserInterface $user, string $size = null, array $parameters = []): string|false
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ((null === $avatar = $user->getAvatar()) || !$avatar instanceof MediaInterface) {
            return false;
        }

        if (is_string($size)) {
            $size = AvatarSize::tryFrom($size);
        }

        $asset = $this->thumbReference($user->getAvatar(), value($size));
        if (null === $asset) {
            return false;
        }

        if (null !== $size) {
            $asset = \str_replace('192', value($size), $asset);
        }
        if (!$this->assetExists($asset)) {
            return false;
        }

        return $this->assetImage(\ltrim($asset, '/'), $parameters);
    }

    /**
     * Returns the public url/path of an asset.
     */
    private function assetUrl(string $path, string $packageName = null): string
    {
        return $this->extension->getAssetUrl($path, $packageName);
    }

    /**
     * Gets the version for the given path.
     */
    private function assetVersion(?string $path): int
    {
        if ($this->debug) {
            return $this->version;
        }
        $realPath = $this->getRealPath($path);
        if (null === $realPath) {
            return $this->version;
        }
        if (!isset($this->versions[$realPath])) {
            $this->versions[$realPath] = (int) \filemtime($realPath);
        }

        return $this->versions[$realPath];
    }

    /**
     * Gets the real (absolute) file path or null if not exist.
     */
    private function getRealPath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $path = FileUtils::buildPath($this->webDir, $path);
        $file = \realpath($path);
        if (false === $file) {
            return null;
        }
        if (!FileUtils::isFile($file)) {
            return null;
        }

        return FileUtils::normalize($file);
    }

    /**
     * Gets the image size.
     *
     * @return array{0: int, 1: int}
     */
    private function imageSize(string $path): array
    {
        $full_path = (string) $this->getRealPath($path);

        return $this->getImageSize($full_path);
    }

    /**
     * Reduce parameters.
     *
     * @param array<string, string|int> $parameters
     */
    private function reduceParams(array $parameters): string
    {
        $callback = static fn (string $key, string|int $value): string => \sprintf('%s="%s"', $key, \htmlspecialchars((string) $value));

        return \implode(' ', \array_map($callback, \array_keys($parameters), \array_values($parameters)));
    }

    /**
     * Gets an asset with version.
     */
    private function versionedAsset(string $path, string $packageName = null): string
    {
        $url = $this->assetUrl($path, $packageName);
        $version = $this->assetVersion($path);

        return \sprintf('%s?version=%d', $url, $version);
    }
}
