<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Twig\Extension;

use Talav\ProfileBundle\Twig\Runtime\ProfileExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProfileExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
	        new TwigFunction('get_friendship_count', [ProfileExtensionRuntime::class, 'getFriendshipCount']),
            new TwigFunction('get_user_friendship_status', [ProfileExtensionRuntime::class, 'getUserFriendshipStatus']),
            new TwigFunction('is_user_friend', [ProfileExtensionRuntime::class, 'isFriend']),
            new TwigFunction('is_user_blocked', [ProfileExtensionRuntime::class, 'isBlocked']),
            new TwigFunction('get_reputation_total', [ProfileExtensionRuntime::class, 'getReputationTotal']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('username', [ProfileExtensionRuntime::class, 'username'], ['is_safe' => ['html']]),
        ];
    }
}
