<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
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
use Talav\CoreBundle\Utils\TypeCast;
use Talav\ProfileBundle\Entity\UserRelation;
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
        if (false) {
            $user1 = $this->userManager->findUserByUsername('root');
            $user2 = $this->userManager->findUserByUsername('user0');
            dd($this->entityManager->getRepository(UserRelation::class)->findRelationBetween($user1, $user2));
        }

        if (false) {

        /** @var \Talav\UserBundle\Model\UserInterface $user */
        $user = $this->userManager->findUserByUsername('root');
//        $user->setMetadata('PROFILE_COMPLETED','0');
//        $this->userManager->update($user);

        dd(TypeCast::try($user->getMetadataValue('PROFILE_COMPLETED'), 'string'));

        $user1 = $this->userManager->findUserByUsername('root');
        $user2 = $this->userManager->findUserByUsername('user0');

        dd($this->entityManager->getRepository(UserRelation::class)->findUnconfirmedRelationBetween($user1, $user2));
        }


	    $form = $this->createForm(ProfileEditType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $this->userManager->update($user, true);

            $message = $this->trans('profile.edit.success', ['%username%' => $user->getUserIdentifier()], null, null);
            $this->addFlashMessage(FlashType::SUCCESS, $message);

            return $this->redirectToRoute('user_profile_edit');
        }

        return $this->render('@TalavUser/frontend/profile/profile_edit.html.twig', [
            'form' => $form,
        ]);
    }

//    #[Route(path: '/view/{username}', name: 'user_profile_show', requirements: ['username' => Requirement::ASCII_SLUG])]
//    #[ParamConverter('user', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
    #[Route(path: '/view/{username}', name: 'user_profile_view', requirements: ['username' => Requirement::ASCII_SLUG], defaults: ['username' => null])]
    public function show(string $username = null): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw $this->createAccessDeniedException();
        }

        if (null !== $username) {
            $user = $this->userManager->getRepository()->findOneBy(['username' => $username]);
            if (!$user instanceof \Talav\Component\User\Model\UserInterface) {
                $e = new UserNotFoundException('Пользователь "'.$username.'" не найден.');
                $e->setUserIdentifier($username);
                throw $e;
            }
        } else {
            $user = $currentUser;
        }

        return $this->render('@TalavUser/frontend/profile/profile_view.html.twig', [
            'user' => $user
        ]);


        dd($user);
//        if (!$member->isBrowsable()) {
//            throw new AccessDeniedException();
//        }

        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
//        if ($loggedInMember === $member) {
//            return $this->showOwnProfile($member);
//        }

        return $this->renderProfile(false, $user, $loggedInMember);
    }
}
