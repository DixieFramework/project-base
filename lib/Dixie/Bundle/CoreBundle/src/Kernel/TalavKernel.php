<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Kernel;

use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Talav\CoreBundle\Application\TalavApp;

class TalavKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * If this header is sent, application context uses this identity rather than generating a new one.
     */
    public const CONTEXT_IDENTITY_HEADER = 'X-Context-ID';

    /**
     * Load balancer IP is dynamic and can change anytime.
     * HAProxy is setting this header to each request and removes it on each response,
     * so we can trust it and append it to trusted proxies.
     */
    private const LOAD_BALANCER_IP_HEADER = 'X-LoadBalancer-IP';
    protected int $userIdAdmin = 1;
    protected int $userIdConsole = 2;
    protected int $userIdAnonymous = 3;
    private string $loadBalancerIp = '';
    private string $contextId = '';

    // ---

    /** @var ?string */
    protected ?string $rootDir = null;

    /** @var array<string, string> */
    protected ?array $projectDirs = null;

    public function __construct(
        private readonly string $appNamespace,
        private readonly string $appSystem,
        private readonly string $appVersion,
        private readonly bool $appReadOnlyMode,
        string $environment,
        bool $debug,
    ) {
        parent::__construct(
            environment: $environment,
            debug: $debug
        );

   		self::setProjectRoot();

		$this->rootDir = $this->getRootDir();
		$this->projectDirs = $this->initProjectDirs();
    }

    /**
     * Override to set up static stuff at boot time.
     */
    public function boot(): void
    {
        TalavApp::init(
            appNamespace: $this->appNamespace,
            appSystem: $this->appSystem,
            appVersion: $this->appVersion,
            appReadOnlyMode: $this->appReadOnlyMode,
            projectDir: $this->getProjectDir(),
            appEnv: $this->getEnvironment(),
            userIdAdmin: $this->userIdAdmin,
            userIdConsole: $this->userIdConsole,
            userIdAnonymous: $this->userIdAnonymous,
            contextId: $this->contextId
        );

        parent::boot();

        $trustedProxies = Request::getTrustedProxies();
        if ($this->loadBalancerIp && $trustedProxies) {
            $trustedProxies[] = $this->loadBalancerIp;
            Request::setTrustedProxies($trustedProxies, Request::getTrustedHeaderSet());
        }
    }

    public function handle(
        Request $request,
        int $type = HttpKernelInterface::MAIN_REQUEST,
        bool $catch = true
    ): Response {
        $this->loadBalancerIp = (string) $request->headers->get(self::LOAD_BALANCER_IP_HEADER);
        $this->contextId = (string) $request->headers->get(self::CONTEXT_IDENTITY_HEADER);

        return parent::handle($request, $type, $catch);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir(): string
    {
        if ($this->rootDir === null) {
            $reflection = new ReflectionObject($this);
            $dir = $rootDir = \dirname($reflection->getFileName());

            while (! \file_exists($dir . \DIRECTORY_SEPARATOR . 'composer.json')) {
                if (\dirname($dir) === $dir) {
                    return $this->rootDir = $rootDir;
                }

                $dir = \dirname($dir);
            }

            $this->rootDir = $dir;
        }

        return $this->rootDir;
    }

    public static function setProjectRoot(): void
    {
        // this should already be defined at this point, but we include a fallback for backwards compatibility here
        if (!defined('DIXIE_PROJECT_ROOT')) {
            define(
                'DIXIE_PROJECT_ROOT',
                $_SERVER['DIXIE_PROJECT_ROOT'] ?? $_ENV['DIXIE_PROJECT_ROOT'] ??
                $_SERVER['REDIRECT_DIXIE_PROJECT_ROOT'] ?? $_ENV['REDIRECT_DIXIE_PROJECT_ROOT'] ??
                realpath(__DIR__ . '/../../../../../..')
            );
        }
    }

    /**
     * Merge composer project dir settings with the default ria dir settings.
     *
     * @return array<string, string>
     */
    protected function initProjectDirs(): array
    {
        self::prepareEnvVariables();

        $resolveConstant = function (string $name, $default, bool $define = true) {
            // return constant if defined
            if (defined($name)) {
                return constant($name);
            }

            $value = $_SERVER[$name] ?? $default;
            if ($define) {
                define($name, $value);
            }

            return $value;
        };

        // basic paths
        $resolveConstant('DIXIE_COMPOSER_PATH', DIXIE_PROJECT_ROOT . '/vendor');
        $resolveConstant('DIXIE_COMPOSER_FILE_PATH', DIXIE_PROJECT_ROOT);
        $resolveConstant('DIXIE_PATH', realpath(__DIR__ . '/..'));
        $resolveConstant('DIXIE_WEB_ROOT', DIXIE_PROJECT_ROOT . '/public');
        $resolveConstant('DIXIE_PRIVATE_VAR', DIXIE_PROJECT_ROOT . '/var');

        if ($this->projectDirs === null) {
            $jsonFile = $this->rootDir . \DIRECTORY_SEPARATOR . 'composer.json';
            $dirs = [
                'DIXIE_PATH' => $this->rootDir,
                'DIXIE_WEB_ROOT' => $this->rootDir . \DIRECTORY_SEPARATOR . 'public',
                'DIXIE_PRIVATE_VAR' => $this->rootDir . \DIRECTORY_SEPARATOR . 'var',
                'DIXIE_CONFIGURATION_DIRECTORY' => $this->rootDir . \DIRECTORY_SEPARATOR . 'config',
                'DIXIE_LOG_DIRECTORY' => $this->rootDir . \DIRECTORY_SEPARATOR . 'var' . \DIRECTORY_SEPARATOR . 'log',
                'DIXIE_CACHE_DIRECTORY' => $this->rootDir . \DIRECTORY_SEPARATOR . 'var' . \DIRECTORY_SEPARATOR . 'cache',
                'DIXIE_SYSTEM_TEMP_DIRECTORY' => $this->rootDir . \DIRECTORY_SEPARATOR . 'var' . \DIRECTORY_SEPARATOR . 'tmp',
                'app-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'app',
                'config-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'config',
                'database-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'database',
                'public-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'public',
                'resources-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'resources',
                'routes-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'routes',
                'tests-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'tests',
                'storage-dir' => $this->rootDir . \DIRECTORY_SEPARATOR . 'storage',
            ];

            if (\file_exists($jsonFile)) {
                $jsonData = \json_decode(\file_get_contents($jsonFile), true);
                $extra = $jsonData['extra'] ?? [];

                foreach ($extra as $key => $value) {
                    if (\array_key_exists($key, $dirs)) {
                        $dirs[$key] = $this->rootDir . \DIRECTORY_SEPARATOR . \ltrim($value, '/\\');
                    }
                }
            }

            $this->projectDirs = $dirs;
        }

        return $this->projectDirs;
    }

    private static function prepareEnvVariables(): void
    {
        if (class_exists('Symfony\Component\Dotenv\Dotenv')) {
            (new Dotenv())->bootEnv(DIXIE_PROJECT_ROOT . '/.env');
        } else {
            $_SERVER += $_ENV;
        }
    }
}
