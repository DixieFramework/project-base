<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Twig\Components;

use Groshy\Entity\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('user_friend', template: '@TalavProfile/components/user_friend.html.twig')]
final class UserFriendComponent
{
    public User $user;
}
