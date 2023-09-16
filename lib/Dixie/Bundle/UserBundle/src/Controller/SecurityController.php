<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Form\User\UserLoginType;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller for login user.
 */
#[AsController]
class SecurityController extends AbstractController
{
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '/login', name: 'talav_user_login')]
    #[Route(path: '/login', name: 'app_login')]
    public function login(#[CurrentUser] ?UserInterface $user, AuthenticationUtils $utils): Response
    {
        if ($user instanceof UserInterface) {
            return $this->redirectToHomePage();
        }
        $form = $this->createForm(UserLoginType::class, [
            'username' => $utils->getLastUsername(),
            'remember_me' => true,
        ]);

        return $this->render('@TalavUser/security/login.html.twig', [
            'error' => $utils->getLastAuthenticationError(),
            'form' => $form,
        ]);
    }

    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/logout', name: 'talav_user_logout')]
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \LogicException('This method should never be reached.');
    }
}
