<?php

declare(strict_types=1);

namespace Talav\UserBundle\Provider;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Groshy\Entity\User;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Manager\UserOAuthManagerInterface;
use Talav\Component\User\Model\UserInterface as TalavUserInterface;
use Talav\Component\User\Model\UserOAuthInterface;
use Talav\Component\User\Provider\UserProvider;
use Webmozart\Assert\Assert;

/**
 * Class providing a bridge to use the Talav user provider with HWIOAuth.
 */
class TalavUserProvider extends UserProvider implements UserProviderInterface, AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    protected UserManagerInterface $userManager;

    protected UserOAuthManagerInterface $userOAuthManager;

    public function __construct(UserManagerInterface $userManager, UserOAuthManagerInterface $userOAuthManager)
    {
        $this->userManager = $userManager;
        $this->userOAuthManager = $userOAuthManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $oauth = $this->userOAuthManager->findOneByProviderIdentifier(
            $response->getResourceOwner()->getName(),
            $response->getUsername()
        );
        if ($oauth instanceof UserOAuthInterface) {
            return $oauth->getUser();
        }
        if (null !== $response->getEmail()) {
            $user = $this->userManager->findUserByUsernameOrEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }

            return $this->createUserByOAuthUserResponse($response);
        }

        throw new UserNotFoundException('Email is null or not provided');
    }

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $exceptionMessage = \sprintf(
            'Unable to find an Sulu\Component\Security\Authentication\UserInterface object identified by %s',
            $identifier
        );

        try {
            $user = $this->userManager->getRepository()->findUserByIdentifier($identifier);

            if (!$user->getEnabled()) {
                throw new DisabledException('User is not enabled yet.');
            }

            if ($user->getLocked()) {
                throw new LockedException('User is locked.');
            }

            return $user;
        } catch (NoResultException $e) {
            throw new UserNotFoundException($exceptionMessage, 0, $e);
        }
    }

    public function refreshUser(UserInterface $user)
    {
        $userClass = $this->userManager->getClassName();
        if (!$user instanceof $userClass) {
            throw new UnsupportedUserException(sprintf(
                'Expected an instance of %s, but got "%s".',
                $userClass,
                get_class($user)
            ));
        }

        // Refresh user should revert entity back to it's initial state using non changed field;
        // otherwise, entity may be replaced with another as some field may be changed in memory.
        // Example: a user changed username, the change was rejected by validation but in memory value was changed.
        // Calling to refreshUser and using username as criteria will lead to user replacing with another user.
        // UoW has internal identity cache which will not actually reload user just by calling to findOneBy.
        try {
            // try to reload existing entity to revert it's state to initial
            $this->userManager->getEntityManager()->refresh($user);
        } catch (ORMInvalidArgumentException $e) {
            // if entity is not managed and can not be reloaded - load it by ID from the database
            $user = $this->userManager->getEntityManager()->find($userClass, $user->getId());
        }

        if (null === $user) {
            throw new UserNotFoundException('User can not be loaded.');
        }

        return $user;



        $class = \get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                \sprintf(
                    'Instance of "%s" are not supported.',
                    $class
                )
            );
        }

        $user = $this->userManager->getRepository()->findUserWithSecurityById($user->getId());

        if (!$user->isEnabled()) {
            throw new DisabledException('User is not enabled yet.');
        }

        if (!$user->isVerified()) {
            throw new LockedException('User is locked.');
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        return is_a($class, TalavUserInterface::class, true);
//        return \is_subclass_of($class, UserInterface::class);
    }

    /**
     * Ad-hoc creation of user.
     */
    private function createUserByOAuthUserResponse(UserResponseInterface $response): TalavUserInterface
    {
        /** @var TalavUserInterface $user */
        $user = $this->userManager->create();

        // set default values taken from OAuth sign-in provider account
        $user->setEmail($response->getEmail());
        if (null !== $name = $response->getFirstName()) {
            $user->setFirstName($name);
        } elseif (null !== $realName = $response->getRealName()) {
            $user->setFirstName($realName);
        }
        if (null !== $lastName = $response->getLastName()) {
            $user->setLastName($lastName);
        }
        if (!$user->getUsername()) {
            $user->setUsername($this->generateRandomUsername($response->getResourceOwner()->getName()));
        }

        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPassword(substr(sha1($response->getAccessToken()), 0, 30));
        $user->setEnabled(true);

        return $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Attach OAuth sign-in provider account to existing user.
     */
    private function updateUserByOAuthUserResponse(
        TalavUserInterface $user,
        UserResponseInterface $response
    ): TalavUserInterface {
        Assert::isInstanceOf($user, TalavUserInterface::class);

        /** @var UserOAuthInterface $oauth */
        $oauth = $this->userOAuthManager->create();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setRefreshToken($response->getRefreshToken());

        $user->addOAuthAccount($oauth);
        $this->userManager->update($user, true);

        return $user;
    }

    /**
     * Generates a random username with the given
     * e.g github_user12345, facebook_user12345.
     *
     * @param string $serviceName
     */
    private function generateRandomUsername($serviceName): string
    {
        return $serviceName.'_'.substr(uniqid((rand()), true), 10);
    }
}
