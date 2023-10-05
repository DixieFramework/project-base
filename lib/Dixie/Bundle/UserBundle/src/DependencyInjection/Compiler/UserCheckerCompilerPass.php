<?php

declare(strict_types=1);

namespace Talav\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserCheckerCompilerPass implements CompilerPassInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container)
	{
		$filterManager = $container->getDefinition('talav.user.security.user_checker_observer');
		$definitions = $container->findTaggedServiceIds('talav.user.security.user_checker');

		foreach ($definitions as $id => $tagInfo) {
			$filterManager->addMethodCall('add', [new Reference($id)]);
		}
	}
}
