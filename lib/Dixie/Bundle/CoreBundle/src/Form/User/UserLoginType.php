<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Talav\CoreBundle\Form\FormHelper;
//use Talav\CoreBundle\Service\ApplicationService;
use Talav\CoreBundle\Service\CaptchaImageService;

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

    protected function getLabelPrefix(): ?string
    {
        return 'security.login.';
    }
}
