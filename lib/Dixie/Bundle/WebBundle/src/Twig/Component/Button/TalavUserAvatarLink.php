<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class UserAvatarLink.
 */
#[AsTwigComponent(template: '@TalavWeb/components/button/user_avatar_link.html.twig')]
final class TalavUserAvatarLink
{
    public string $username;
    public string $surname;
    public string $path;
    public ?string $avatarUrl;
}
