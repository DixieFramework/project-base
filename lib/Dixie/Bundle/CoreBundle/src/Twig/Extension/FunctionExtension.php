<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Entity\AbstractEntity;
//use Talav\CoreBundle\Interfaces\UserInterface;
use Talav\CoreBundle\Service\NonceService;
//use Talav\CoreBundle\Service\UrlGeneratorService;
use Talav\CoreBundle\Traits\ImageSizeTrait;
use Talav\CoreBundle\Utils\FileUtils;
use Talav\CoreBundle\Utils\StringUtils;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Talav\UserBundle\Model\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Twig extension for assets.
 */
final class FunctionExtension extends AbstractExtension
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

    /**
     * Constructor.
     */
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
        private readonly UploaderHelper $helper,
        private readonly UrlGeneratorInterface $generator,
    ) {
        if (FileUtils::exists($composer_file) && \is_int($version = \filemtime($composer_file))) {
            $this->version = $version;
        }
        $this->webDir = FileUtils::normalize($webDir);
    }

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            // assets
            new TwigFunction('asset_exists', $this->assetExists(...)),
            new TwigFunction('asset_js', $this->assetJs(...), $options),
            new TwigFunction('asset_css', $this->assetCss(...), $options),
            new TwigFunction('asset_icon', $this->assetIcon(...), $options),
            new TwigFunction('asset_image', $this->assetImage(...), $options),
            new TwigFunction('asset_versioned', $this->versionedAsset(...), $options),
            new TwigFunction('asset_image_user', $this->assetImageUser(...), $options),

            // routes
            new TwigFunction('cancel_url', $this->cancelUrl(...)),
            new TwigFunction('route_params', $this->routeParams(...)),
        ];
    }

    /**
     * Output a link style sheet tag with a version and nonce.
     *
     * @param array<string, string|int> $parameters
     *
     * @throws \Exception
     */
    private function assetCss(string $path, array $parameters = [], string $packageName = null): string
    {
        $href = $this->versionedAsset($path, $packageName);
        $parameters = \array_merge([
            'href' => $href,
            'rel' => 'stylesheet',
            'nonce' => $this->getNonce(),
        ], $parameters);
        $attributes = $this->reduceParams($parameters);

        return "<link $attributes>";
    }

    /**
     * Checks if the given asset path exists.
     */
    private function assetExists(?string $path): bool
    {
        if (null === $file = $this->getRealPath($path)) {
            return false;
        }

        return StringUtils::startWith($file, $this->webDir);
    }

    /**
     * Gets an application icon.
     */
    private function assetIcon(int $size, string $packageName = null): string
    {
        $path = \sprintf('images/icons/favicon-%1$dx%1$d.png', $size);

        return $this->assetUrl($path, $packageName);
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
    private function assetImageUser(?UserInterface $user, string $size = null, array $parameters = []): string|false
    {
        if (!$user instanceof UserInterface) {
            return false;
        }
        $asset = $this->helper->asset($user);
        if (null === $asset) {
            return false;
        }
        if (null !== $size) {
            $asset = \str_replace('192', $size, $asset);
        }
        if (!$this->assetExists($asset)) {
            return false;
        }

        return $this->assetImage(\ltrim($asset, '/'), $parameters);
    }

    /**
     * Output a javascript source tag with a version and nonce.
     *
     * @param array<string, string|int> $parameters
     *
     * @throws \Exception
     */
    private function assetJs(string $path, array $parameters = [], string $packageName = null): string
    {
        $parameters = \array_merge([
            'src' => $this->versionedAsset($path, $packageName),
            'nonce' => $this->getNonce(),
        ], $parameters);
        $attributes = $this->reduceParams($parameters);

        return "<script $attributes></script>";
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
        if ($this->debug || null === $realPath = $this->getRealPath($path)) {
            return $this->version;
        }
        if (!isset($this->versions[$realPath])) {
            $this->versions[$realPath] = (int) \filemtime($realPath);
        }

        return $this->versions[$realPath];
    }

    /**
     * Gets the cancel URL.
     */
    private function cancelUrl(Request $request, AbstractEntity|int|null $id = 0, string $defaultRoute = AbstractController::HOME_PAGE): string
    {
        return $this->generator->cancelUrl($request, $id, $defaultRoute);
    }

    /**
     * @throws \Exception
     */
    private function getNonce(): string
    {
        return $this->service->getNonce();
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
        if (false === $file = \realpath($path)) {
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
     * Gets the route parameters.
     */
    private function routeParams(Request $request, AbstractEntity|int|null $id = 0): array
    {
        return $this->generator->routeParams($request, $id);
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
