<?php

declare(strict_types=1);

namespace Talav\ResourceBundle\DependencyInjection;

use DoctrineExtensions\Query\Mysql;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TalavResourceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

	    $configs = $container->getExtensionConfig($this->getAlias());

		$this->configureDoctrine($container);
	}

	private function configureDoctrine(ContainerBuilder $container): void
	{
		$container->prependExtensionConfig('doctrine', [
			'orm'  => [
				'entity_managers' => [
					'default' => [
						'dql' => [
							'datetime_functions' => [
								'month'          => Mysql\Month::class,
								'year'           => Mysql\Year::class,
								'date'           => Mysql\Date::class,
								'day'            => Mysql\Day::class,
								'dayofweek'      => Mysql\DayOfWeek::class,
								'dayofyear'      => Mysql\DayOfYear::class,
								'unix_timestamp' => Mysql\UnixTimestamp::class,
							],
							'numeric_functions'  => [
								'rand' => Mysql\Rand::class,
							],
							'string_functions'   => [
								'ifnull' => Mysql\IfNull::class,
							],
						],
					],
				],
			],
		]);
	}
}
