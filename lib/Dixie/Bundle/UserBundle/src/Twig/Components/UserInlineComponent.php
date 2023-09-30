<?php

declare(strict_types=1);

namespace Talav\UserBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Talav\Component\User\Model\UserInterface;

#[AsTwigComponent('user_inline', template: '@TalavUser/components/user_inline.html.twig')]
final class UserInlineComponent
{
    public UserInterface $user;
    public bool $showAvatar = true;
}
