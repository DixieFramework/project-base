<?php

namespace Groshy;

use Groshy\DependencyInjection\GroshyExtension;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Talav\CoreBundle\Kernel\TalavKernel;

class Kernel extends TalavKernel//extends BaseKernel
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

	protected function build(ContainerBuilder $container): void
	{
		$container->registerExtension(new GroshyExtension());
	}
}
