<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\CoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\Interfaces\UserInterface;

/**
 * Username type.
 *
 * @extends AbstractType<TextType>
 */
class UserNameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('attr', [
            'autocomplete' => 'username',
            'minLength' => UserInterface::MIN_USERNAME_LENGTH,
            'maxLength' => UserInterface::MAX_USERNAME_LENGTH,
        ])->setDefault('prepend_icon', 'fa-fw fa-regular fa-user');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return TextType::class;
    }
}
