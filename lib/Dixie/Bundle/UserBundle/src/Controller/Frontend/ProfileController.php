<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller\Frontend;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Enums\FlashType;
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
	use SettingManagerAwareTrait;

	public function __construct(
        private UserManagerInterface $userManager,
        private TranslatorInterface $translator,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * Change password of the current user (if any).
     */
    #[Route(path: '/change-password', name: 'user_profile_change_password')]
    public function changePassword(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProfileChangePasswordType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $manager->flush();

            $message = $this->trans('profile.change_password.success', ['%username%' => $user->getUserIdentifier()], null, null);
            $this->addFlashMessage(FlashType::SUCCESS, $message);

            return $this->redirectToRoute('user_profile_change_password');
            return $this->redirectToHomePage('profile.change_password.success', ['%username%' => $user->getUserIdentifier()]);
        }

        return $this->render('@TalavUser/frontend/profile/profile_change_password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Edit the profile of the current user (if any).
     */
    #[Route(path: '/edit', name: 'user_profile_edit')]
    public function editProfil(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager): Response
    {
		dd($this->getUser()->hasRole('ROLE_USER'));

	    dd($this->settingManager->all());

	    $form = $this->createForm(ProfileEditType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $this->userManager->update($user, true);

            $message = $this->trans('profile.edit.success', ['%username%' => $user->getUserIdentifier()], null, null);
            $this->addFlashMessage(FlashType::SUCCESS, $message);

            return $this->redirectToRoute('user_profile_edit');
            return $this->redirectToHomePage('profile.edit.success', ['%username%' => $user->getUserIdentifier()]);
        }

        return $this->render('@TalavUser/frontend/profile/profile_edit.html.twig', [
            'form' => $form,
        ]);
    }
}
