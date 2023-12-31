<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\CoreBundle\Form\AbstractEntityType;
use Talav\CoreBundle\Form\FormHelper;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Talav\UserBundle\Entity\User;

/**
 * Type to change the password of the current (logged) user.
 *
 * @template-extends AbstractEntityType<User>
 */
class ProfileChangePasswordType extends AbstractEntityType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'constraints' => [
                new Callback(function (User $user, ExecutionContextInterface $context): void {
                    $this->validate($context);
                }),
            ],
            'translation_domain' => 'TalavUserBundle'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    protected function addFormFields(FormHelper $helper): void
    {
        $helper->field('currentPassword')
            ->addCurrentPasswordType();

        $helper->field('plainPassword')
            ->addRepeatPasswordType('user.password.new', 'user.password.new_confirmation');

        $helper->field('checkPassword')
            ->label('user.change_password.check_password')
            ->notRequired()
            ->notMapped()
            ->addCheckboxType();

        // username for ajax validation
        $helper->field('username')->addHiddenType();
    }

    protected function getLabelPrefix(): ?string
    {
        return null;
    }

    /**
     * Conditional validation depending on the check password checkbox.
     */
    private function validate(ExecutionContextInterface $context): void
    {
        /** @psalm-var FormInterface<mixed> $root */
        $root = $context->getRoot();

        // must check password?
        if (!$root->get('checkPassword')->getData()) {
            return;
        }

        // check password
        $password = $root->get('plainPassword');
        $violations = $context->getValidator()->validate(
            $password->getData(),
            new NotCompromisedPassword()
        );

        // if compromised assign the error to the password field
        if ($violations->count() > 0 && \method_exists($violations, '__toString')) {
            $password->addError(new FormError((string) $violations));
        }
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'translation_domain' => 'TalavUserBundle'
//        ]);
//    }
}
