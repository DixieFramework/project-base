<?php

declare(strict_types=1);

namespace Talav\ResourceBundle\DependencyInjection;

use DoctrineExtensions\Query\Mysql;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Talav\Component\Resource\Factory\Factory;
use Talav\Component\Resource\Metadata\Resource as ResourceMetadata;
use Talav\Component\Resource\Reflection\ClassReflection;

use function Symfony\Component\String\u;

class TalavResourceExtension extends Extension implements PrependExtensionInterface
{
    use PrependBundleConfigTrait;

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->configureDoctrine($container);

        $this->prependBundleConfigFiles($container);
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('talav.resource.mapping', $config['mapping']);
//        $this->autoRegisterResources($config, $container);
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
