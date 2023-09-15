<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ContainerAwareTrait
{
	protected ContainerInterface $container;

	#[Required]
	public function setContainer(ContainerInterface $container): void
	{
		$this->container = $container;
	}
}
