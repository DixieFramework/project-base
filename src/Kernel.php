<?php

namespace Groshy;

use Groshy\DependencyInjection\GroshyExtension;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
	use MicroKernelTrait;

	/**
	 * The version for this release of core.
	 */
	public const VERSION = '1.0.0-DEV';

	/**
	 * The minimum required PHP version for this release of core.
	 */
	public const PHP_MINIMUM_VERSION = '8.1.14';

	/** @var ?string */
	protected ?string $rootDir = null;

	/** @var array<string, string> */
	protected ?array $projectDirs = null;

	/**
	 * Constructor.
	 *
	 * @param string $environment
	 * @param bool   $debug
	 */
	public function __construct(string $environment, bool $debug)
	{
		parent::__construct($environment, $debug);

		self::setProjectRoot();

		$this->rootDir = $this->getRootDir();
		$this->projectDirs = $this->initProjectDirs();
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
				realpath(__DIR__ . '/..')
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

	protected function build(ContainerBuilder $container): void
	{
		$container->registerExtension(new GroshyExtension());
	}
}
