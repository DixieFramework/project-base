<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Talav\CoreBundle\Form\FormHelper;
use Talav\CoreBundle\Service\ApplicationService;
use Talav\CoreBundle\Service\CaptchaImageService;
use Talav\CoreBundle\Traits\TranslatorAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Talav\UserBundle\Entity\User;

/**
 * Type to register a new user.
 */
class UserRegistrationType extends AbstractUserCaptchaType implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    use TranslatorAwareTrait;

    /**
     * Constructor.
     */
    public function __construct(CaptchaImageService $service, #[Autowire('%talav_user.registration.display_captcha%')] bool $displayCaptcha)
    {
        parent::__construct($service, $displayCaptcha);
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefault('data_class', User::class);
//    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'TalavUserBundle'
        ]);
    }

    protected function addFormFields(FormHelper $helper): void
    {
        $helper->field('username')
            ->label('user.fields.username')
            ->addUserNameType();

        $helper->field('email')
            ->label('user.fields.email')
            ->addEmailType();

        $helper->field('plainPassword')
            ->addRepeatPasswordType();

        parent::addFormFields($helper);

        $helper->field('agreeTerms')
            ->notMapped()
            ->rowClass('mb-0')
            ->label('registration.agreeTerms.label')
            ->updateAttribute('data-error', $this->trans('registration.agreeTerms.error'))
            ->addCheckboxType();
    }

    protected function getLabelPrefix(): ?string
    {
        return 'user.fields.';
    }
}
