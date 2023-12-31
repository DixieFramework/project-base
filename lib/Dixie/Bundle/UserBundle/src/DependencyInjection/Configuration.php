<?php

declare(strict_types=1);

namespace Talav\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\Component\User\Canonicalizer\Canonicalizer;
use Talav\Component\User\Manager\LoginManager;
use Talav\Component\User\Manager\UserManager;
use Talav\Component\User\Manager\UserOAuthManager;
use Talav\Component\User\Repository\LoginAttemptRepository;
use Talav\Component\User\Repository\UserRepository;
use Talav\Component\User\Util\PasswordUpdater;
use Talav\Component\User\Util\TokenGenerator;
use Talav\UserBundle\Entity\LoginAttempt;
use Talav\UserBundle\Entity\User;
use Talav\UserBundle\Entity\UserOAuth;
use Talav\UserBundle\Form\Model\RegistrationFormModel;
use Talav\UserBundle\Form\Type\RegistrationFormType;
use Talav\UserBundle\Mailer\UserMailer;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('talav_user');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('login_route')->defaultValue('talav_user_login')->end()
                ->scalarNode('login_redirect_route')->defaultValue('parthenon_user_profile')->end()
                ->scalarNode('signup_success_route')->defaultValue('parthenon_user_signed_up')->end()
                ->scalarNode('firewall_name')->defaultValue('main')->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user')->cannotBeEmpty()->defaultValue(User::class)->end()
                    ->end()
                ->end()
                ->arrayNode('mailer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(UserMailer::class)->end()
                    ->end()
                ->end()
                ->arrayNode('login_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(LoginManager::class)->end()
                    ->end()
                ->end()
                ->arrayNode('canonicalizer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(Canonicalizer::class)->end()
                    ->end()
                ->end()
                ->arrayNode('password_updater')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(PasswordUpdater::class)->end()
                    ->end()
                ->end()
                ->arrayNode('token_generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(TokenGenerator::class)->end()
                    ->end()
                ->end()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('retry_ttl')->defaultValue('2 minutes')->end()
                        ->scalarNode('token_ttl')->defaultValue('24 hours')->end()
                    ->end()
                ->end()
                ->arrayNode('email')
                    ->isRequired()
                    ->children()
                        ->arrayNode('from')
                            ->children()
                                ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('display_captcha')->defaultValue(false)->end()
                ->integerNode('password_strength_level')->defaultValue(2)->end()
            ->end()
        ;

        $this->addResourceSection($treeBuilder->getRootNode());
        $this->addRegistrationSection($treeBuilder->getRootNode());
        $this->addLoginSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(User::class)->end()
                                        ->scalarNode('manager')->defaultValue(UserManager::class)->end()
                                        ->scalarNode('repository')->defaultValue(UserRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_oauth')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(UserOAuth::class)->end()
                                        ->scalarNode('manager')->defaultValue(UserOAuthManager::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_login_attempt')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(LoginAttempt::class)->end()
//                                        ->scalarNode('manager')->defaultValue(UserOAuthManager::class)->end()
                                        ->scalarNode('repository')->defaultValue(LoginAttemptRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addRegistrationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
//                        ->arrayNode('confirmation')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->booleanNode('enabled')->defaultFalse()->end()
//                                ->scalarNode('template')->defaultValue('@FOSUser/Registration/email.txt.twig')->end()
//                                ->arrayNode('from_email')
//                                    ->canBeUnset()
//                                    ->children()
//                                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
//                                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
//                                    ->end()
//                                ->end()
//                            ->end()
//                        ->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue(\Talav\CoreBundle\Form\User\UserRegistrationType::class)->end()
                                ->scalarNode('model')->defaultValue(RegistrationFormModel::class)->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['Registration', 'Default'])
                                ->end()
                            ->end()
                        ->end()
                        ->booleanNode('email')->defaultValue(true)->end()
                        ->booleanNode('display_captcha')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addLoginSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('login')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                    ->arrayNode('options')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('username_parameter')->defaultValue('_username')->end()
                            ->scalarNode('password_parameter')->defaultValue('_password')->end()
                            ->scalarNode('check_path')->defaultValue('/login')->end()
                            ->scalarNode('login_path')->defaultValue('/login')->end()
                            ->booleanNode('post_only')->defaultTrue()->end()
                            ->booleanNode('form_only')->defaultFalse()->end()
                            ->booleanNode('use_forward')->defaultFalse()->end()
                            ->booleanNode('enable_csrf')->defaultFalse()->end()
                            ->scalarNode('csrf_parameter')->defaultValue('_csrf_token')->end()
                            ->scalarNode('csrf_token_id')->defaultValue('authenticate')->end()
                        ->end()
                    ->end()
//                        ->arrayNode('form')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->scalarNode('type')->defaultValue(RegistrationFormType::class)->end()
//                                ->scalarNode('model')->defaultValue(RegistrationFormModel::class)->end()
//                                ->arrayNode('validation_groups')
//                                    ->prototype('scalar')->end()
//                                    ->defaultValue(['Registration', 'Default'])
//                                ->end()
//                            ->end()
//                        ->end()
                        ->booleanNode('display_captcha')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
