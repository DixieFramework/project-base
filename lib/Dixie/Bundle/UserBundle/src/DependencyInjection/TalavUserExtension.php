<?php

declare(strict_types=1);

namespace Talav\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\Component\User\Canonicalizer\CanonicalizerInterface;
use Talav\Component\User\Util\PasswordUpdaterInterface;
use Talav\Component\User\Util\TokenGeneratorInterface;
use Talav\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Talav\ResourceBundle\DependencyInjection\PrependBundleConfigTrait;
use Talav\UserBundle\EventSubscriber\WelcomeEmailSubscriber;
use Talav\UserBundle\Mailer\UserMailer;
use Talav\UserBundle\Mailer\UserMailerInterface;

class TalavUserExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
	use PrependBundleConfigTrait;

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

	    // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->autowire(UserMailerInterface::class, $config['mailer']['class']);
        $container->autowire(CanonicalizerInterface::class, $config['canonicalizer']['class']);
        $container->autowire(PasswordUpdaterInterface::class, $config['password_updater']['class']);
        $container->autowire(TokenGeneratorInterface::class, $config['token_generator']['class']);

        if (UserMailer::class == $config['mailer']['class']) {
            $definition = $container->getDefinition(UserMailerInterface::class);
            $definition->setArgument(5, [
                'email' => $config['email']['from']['email'],
                'name' => $config['email']['from']['name'],
            ]);
            $container->setParameter('talav_user.mailer.parameters', $config['email']['from']);
        }

        // WelcomeEmailSubscriber is registered by default, remove it if config does not require confirmation email
        if (!$config['registration']['email']) {
            $container->removeDefinition(WelcomeEmailSubscriber::class);
        }

        if (isset($config['login_route'])) {
            $container->setParameter('talav_user.login_route', $config['login_route']);
        }

        if (isset($config['signup_success_route'])) {
            $container->setParameter('talav_user.signup_success_route', $config['signup_success_route']);
        }

        if (isset($config['firewall_name'])) {
            $container->setParameter('talav_user.firewall_name', $config['firewall_name']);
        }

        $container->setParameter('talav_user.display_captcha', $config['display_captcha']);
        $container->setParameter('talav_user.password_strength_level', $config['password_strength_level']);
        $container->setParameter('talav_user.resetting.retry_ttl', $config['resetting']['retry_ttl']);
        $container->setParameter('talav_user.resetting.token_ttl', $config['resetting']['token_ttl']);
        $container->setParameter('talav_user.registration.form_type', $config['registration']['form']['type']);
        $container->setParameter('talav_user.registration.form_model', $config['registration']['form']['model']);
        $container->setParameter('talav_user.registration.form_validation_groups', $config['registration']['form']['validation_groups']);
        $container->setParameter('talav_user.registration.display_captcha', $config['registration']['display_captcha']);
        $container->setParameter('talav_user.login.options', $config['login']['options']);
        $container->setParameter('talav_user.login.display_captcha', $config['login']['display_captcha']);

        $this->registerResources('app', $config['resources'], $container);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependBundleConfigFiles($container);
//        dd($container->getExtensionConfig('doctrine'));

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach ($container->getExtensionConfig('doctrine') as $config) {
                // do not register mappings if dbal not configured.
                if (false == empty($config['dbal'])) {
                    //$rc = new \ReflectionClass('Payum\Core\Gateway');
                    //$payumRootDir = dirname($rc->getFileName());

                    $container->prependExtensionConfig('doctrine', array(
                        'orm' => array(
                            'mappings' => array(
                                'Talav\Component\User\ValueObject' => array(
                                    'is_bundle' => false,
                                    'type' => 'xml',
                                    'dir' => '%kernel.project_dir%/lib/Dixie/Component/User/src/Doctrine/Mapping/ValueObject',//$payumRootDir.'/Bridge/Doctrine/Resources/mapping',
                                    'prefix' => 'Talav\Component\User\ValueObject',
                                ),
                            ),
                        ),
                    ));

                    break;
                }
            }
        }
    }
}
