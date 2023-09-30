<?php

declare(strict_types=1);

namespace Talav\UserBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Talav\Component\User\Model\UserInterface;

#[AsTwigComponent('user_avatar', template: '@TalavUser/components/user_avatar.html.twig')]
final class UserAvatarComponent
{
    public int $width = 32;
    public int $height = 32;
    public UserInterface $user;
    public bool $asLink = false;
}
