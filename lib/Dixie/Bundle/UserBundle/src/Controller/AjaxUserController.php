<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CoreBundle\Interfaces\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for user XMLHttpRequest (Ajax) calls.
 */
#[AsController]
#[Route(path: '/ajax')]
class AjaxUserController extends AbstractController
{
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly UserManagerInterface $userManager)
    {
    }

    /**
     * Check if a user e-mail already exists.
     */
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/check/user/email', name: 'ajax_check_user_email', methods: Request::METHOD_GET)]
    public function checkEmail(
        #[MapQueryParameter]
        string $email = null,
        #[MapQueryParameter(flags: \FILTER_NULL_ON_FAILURE)]
        string $id = null,
    ): JsonResponse {
        $message = null;
        if (empty($email)) {
            $message = 'email.blank';
        } elseif (\strlen($email) < UserInterface::MIN_USERNAME_LENGTH) {
            $message = 'email.short';
        } elseif (\strlen($email) > UserInterface::MAX_USERNAME_LENGTH) {
            $message = 'email.long';
        } else {
            $user = $this->userManager->findUserByEmail($email);

            if ($user instanceof \Talav\Component\User\Model\UserInterface && $id !== $user->getId()->toString()) {
                $message = 'email.already_used';
            }
        }

        return $this->getJsonResponse($message);
    }

    /**
     * Check if a username already exists.
     */
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/check/user/username', name: 'ajax_check_user_name', methods: Request::METHOD_GET)]
    public function checkName(
        #[MapQueryParameter]
        string $username = null,
        #[MapQueryParameter(flags: \FILTER_NULL_ON_FAILURE)]
        string $id = null,
    ): JsonResponse {
        $message = null;
        if (empty($username)) {
            $message = 'username.blank';
        } elseif (\strlen($username) < UserInterface::MIN_USERNAME_LENGTH) {
            $message = 'username.short';
        } elseif (\strlen($username) > UserInterface::MAX_USERNAME_LENGTH) {
            $message = 'username.long';
        } else {
            $user = $this->userManager->findUserByUsername($username);
            if ($user instanceof \Talav\Component\User\Model\UserInterface && $id !== $user->getId()->toString()) {
                $message = 'username.already_used';
            }
        }

        return $this->getJsonResponse($message);
    }

    /**
     * Check if a username or user e-mail exist.
     */
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '/check/user', name: 'ajax_check_user', methods: Request::METHOD_GET)]
    public function checkUser(#[MapQueryParameter] string $user = null): JsonResponse
    {
        $message = null;
        if (empty($user)) {
            $message = 'username.blank';
        } elseif (!$this->findByUsernameOrEmail($user) instanceof \Talav\Component\User\Model\UserInterface) {
            $message = 'username.not_found';
        }

        return $this->getJsonResponse($message);
    }

    private function findByEmail(string $email): ?\Talav\Component\User\Model\UserInterface
    {
        return $this->userManager->findUserByEmail($email);
    }

    private function findByUsername(string $username): ?\Talav\Component\User\Model\UserInterface
    {
        return $this->userManager->findUserByUsername($username);
    }

    private function findByUsernameOrEmail(string $usernameOrEmail): ?\Talav\Component\User\Model\UserInterface
    {
        return $this->userRepository->findByUsernameOrEmail($usernameOrEmail);
    }

    private function getJsonResponse(string $id = null): JsonResponse
    {
        if (null !== $id) {
            return $this->json($this->trans($id, [], 'validators'));
        }

        return $this->json(true);
    }
}
