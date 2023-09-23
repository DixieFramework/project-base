<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\Form\FormHelper;
//use Talav\CoreBundle\Service\ApplicationService;
use Talav\CoreBundle\Service\CaptchaImageService;
use Talav\UserBundle\Entity\User;

/**
 * User login type.
 */
class UserLoginType extends AbstractUserCaptchaType
{
    /**
     * Constructor.
     */
    public function __construct(CaptchaImageService $service, #[Autowire('%talav_user.login.display_captcha%')] bool $displayCaptcha)
    {
        parent::__construct($service, $displayCaptcha);
    }

    protected function addFormFields(FormHelper $helper): void
    {
        $helper->field('username')
            ->addUserNameType();

        $helper->field('password')
            ->addCurrentPasswordType();

        parent::addFormFields($helper);

        $helper->field('remember_me')
            ->rowClass('float-end')
            ->notRequired()
            ->addCheckboxType();
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'TalavUserBundle',
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_csrf_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'talav_type_user_user_login';
    }

    protected function getLabelPrefix(): ?string
    {
        return 'security.login.';
    }
}
