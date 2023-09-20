<?php

declare(strict_types=1);

namespace Talav\UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Talav\UserBundle\DependencyInjection\Compiler;

class TalavUserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new Compiler\RoleHierarchyPass());
	}
}
