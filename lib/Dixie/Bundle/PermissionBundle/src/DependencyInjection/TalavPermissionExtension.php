<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

final class TalavPermissionExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

	    $this->createPermissionParameter($config['permissions'], $container);

		if (isset($config['permissions'])) {
            $container
                ->getDefinition('talav_permission.security.config_permission_loader')
	            ->setPublic(true)
                ->replaceArgument(0, $container->getParameter('talav.permissions') ?? []);
        }


        $this->registerResources('app', $config['resources'], $container);
    }

	/**
	 * Performs some pre-compilation on the configured permissions from kimai.yaml
	 * to save us from constant array lookups from during runtime.
	 *
	 * @param array $config
	 * @param ContainerBuilder $container
	 */
	private function createPermissionParameter(array $config, ContainerBuilder $container): void
	{
		$names = [];
		// this does not include all possible permission, as plugins do not register them and Kimai defines a couple of
		// permissions as well, which are off by default for all roles
		foreach ($config['sets'] as $set => $permNames) {
			foreach ($permNames as $name) {
				$names[$name] = true;
			}
		}

		$roles = [];
		foreach ($config['maps'] as $role => $sets) {
			foreach ($sets as $set) {
				if (!isset($config['sets'][$set])) {
					$exception = new InvalidConfigurationException(
						'Configured permission set "' . $set . '" for role "' . $role . '" is unknown'
					);
					$exception->setPath('kimai.permissions.maps.' . $role);
					throw $exception;
				}
				$roles[$role] = array_merge($roles[$role] ?? [], $this->extractSinglePermissionsFromSet($config, $set));
			}
		}

		// delete forbidden permissions from roles
		foreach (array_keys($config['maps']) as $name) {
			if (\array_key_exists($name, $config['roles'])) {
				foreach ($config['roles'][$name] as $name2) {
					$roles[$name][$name2] = true;
				}
			}
			$config['roles'][$name] = $roles[$name];
		}

		// make sure to apply all other permissions that might have been registered through plugins
		foreach ($config['roles'] as $role => $perms) {
			$names = array_merge($names, $perms);
		}

		/** @var array<string, array<string>> $roles */
		$securityRoles = $container->getParameter('security.role_hierarchy.roles');
		$roles = [];
		foreach ($securityRoles as $key => $value) {
			$roles[] = $key;
			foreach ($value as $name) {
				$roles[] = $name;
			}
		}

		$container->setParameter('talav.permissions', $config['roles']);
		$container->setParameter('talav.permission_names', $names);
		$container->setParameter('talav.permission_roles', array_map('strtoupper', array_values(array_unique($roles))));
	}

	private function extractSinglePermissionsFromSet(array $permissions, string $name): array
	{
		if (!isset($permissions['sets'][$name])) {
			throw new InvalidConfigurationException('Unknown permission set "' . $name . '"');
		}

		$result = [];

		foreach ($permissions['sets'][$name] as $permissionName) {
			$result[$permissionName] = true;
		}

		return $result;
	}
}
