<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Twig\Runtime;

use Groshy\Entity\User;
use App\Repository\ReputationRepository;
use App\Service\MentionManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ProfileExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly Security $security,
//        private readonly ReputationRepository $reputationRepository,
        private readonly CacheInterface $cache,
//        private readonly MentionManager $mentionManager,
		private readonly UserManagerInterface $userManager,
	    private readonly ManagerInterface $friendshipManager,
    ) {
    }

	public function getFriendshipCount(User $user)
	{
		if (!$this->security->getUser()) {
			return false;
		}

		return $this->friendshipManager->getRepository()->getFriendshipCount($user);
	}

	public function isFriend(User $user)
	{
		if (!$this->security->getUser()) {
			return false;
		}

		return $this->security->getUser()->getProfile()->isFriend($user->getProfile()->getId());
	}

    public function isBlocked(User $blocked)
    {
        if (!$this->security->getUser()) {
            return false;
        }

        return $this->security->getUser()->isBlocked($blocked);
    }

    public function username(string $value, ?bool $withApPostfix = false): string
    {
        return $this->mentionManager->getUsername($value, $withApPostfix);
    }

    public function getReputationTotal(User $user): int
    {
        return $this->cache->get(
            "user_reputation_{$user->getId()}",
            function (ItemInterface $item) use ($user) {
                $item->expiresAfter(60);

                return $this->reputationRepository->getUserReputationTotal($user);
            }
        );
    }
}
