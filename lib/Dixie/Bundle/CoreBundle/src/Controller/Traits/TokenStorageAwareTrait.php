<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller\Traits;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Talav\Component\User\Model\UserInterface;

/**
 * Trait TokenStorageAwareTrait
 *
 * @package Talav\CoreBundle\Controller\Traits
 * @deprecated
 */
trait TokenStorageAwareTrait
{

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Return current use if it exists.
     *
     * @return UserInterface|null
     */
    public function getCurrentUser()
    {
        $user = null;
        $token = $this->tokenStorage->getToken();

        if ($token !== null) {
            $user = $token->getUser();
        }

        if (!$user instanceof UserInterface) {
            throw new \BadMethodCallException('Invalid user object');
        }

        return $user;
    }
}
