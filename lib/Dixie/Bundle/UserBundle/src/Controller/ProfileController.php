<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Form\User\ProfileChangePasswordType;
use Talav\CoreBundle\Form\User\ProfileEditType;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\SettingsBundle\Trait\SettingManagerAwareTrait;

/**
 * Controller for user profile.
 */
#[AsController]
#[Route(path: '/profile')]
#[IsGranted(RoleInterface::ROLE_USER)]
class ProfileController extends AbstractController
{
    /**
     * Change password of the current user (if any).
     */
    #[Route(path: '/change-password', name: 'user_profile_change_password')]
    public function changePassword(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProfileChangePasswordType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $manager->flush();

            return $this->redirectToHomePage('profile.change_password.success', ['%username%' => $user->getUserIdentifier()]);
        }

        return $this->render('@TalavUser/profile/profile_change_password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Edit the profile of the current user (if any).
     */
    #[Route(path: '/edit', name: 'user_user_profile_edit')]
    public function editProfil(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProfileEditType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $manager->flush();

            return $this->redirectToHomePage('profile.edit.success', ['%username%' => $user->getUserIdentifier()]);
        }

        return $this->render('@TalavUser/profile/profile_edit.html.twig', [
            'form' => $form,
        ]);
    }
}
