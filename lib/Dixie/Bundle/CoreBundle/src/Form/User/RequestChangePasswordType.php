<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\Form\FormHelper;

/**
 * Type to request change password of a user.
 */
class RequestChangePasswordType extends AbstractUserCaptchaType
{
    protected function addFormFields(FormHelper $helper): void
    {
        $helper->field('user')
            ->label('resetting.request.user')
            ->widgetClass('user-name')
            ->addUserNameType();

        parent::addFormFields($helper);
    }

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'translation_domain' => 'TalavUserBundle'
		]);
	}
}
