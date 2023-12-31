<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
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
use Talav\GalleryBundle\Entity\GalleryImage;
use Talav\PermissionBundle\Entity\RolePermission;
use Talav\PermissionBundle\Service\RolePermissionManager;
use Talav\PostBundle\Entity\Post;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Repository\ProfileBlockRepository;
use Talav\SettingsBundle\Trait\SettingManagerAwareTrait;
use Talav\UserBundle\Event\FilterUserResponseEvent;
use Talav\UserBundle\Event\FormEvent;
use Talav\UserBundle\Event\GetResponseUserEvent;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserFormEvent;
use Talav\WebBundle\Service\PaginatorService;
use function Symfony\Component\String\u;

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
        private readonly EventDispatcherInterface $eventDispatcher,
        private UserManagerInterface $userManager,
        private TranslatorInterface $translator,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * Change password of the current user (if any).
     */
    #[Route(path: '/change-password', name: 'user_profile_change_password')]
    public function changePassword(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager, RolePermissionManager $rolePermissionManager): Response
    {
//		dd($rolePermissionManager->hasPermission('ROLE_MODERATOR', 'IMAGE_DELETE'));
//		dd($this->entityManager->getRepository(RolePermission::class)->findAll());

//        dd($this->userManager->getRepository()->findUserByIdentifier('user@local.dev'));

        $form = $this->createForm(ProfileChangePasswordType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $manager->flush();

            $message = $this->trans('profile.change_password.success', ['%username%' => $user->getUserIdentifier()], null, null);
            $this->addFlashMessage(FlashType::SUCCESS, $message);

            return $this->redirectToRoute('user_profile_change_password');
        }

        return $this->render('@TalavUser/profile/profile_change_password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Edit the profile of the current user (if any).
     */
    #[Route(path: '/edit', name: 'user_profile_edit')]
    public function editProfil(Request $request, #[CurrentUser] UserInterface $user, EntityManagerInterface $manager, ManagerInterface $userPropertyManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, TalavUserEvents::PROFILE_EDIT_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

	    $form = $this->createForm(ProfileEditType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            return $this->updateUser($request, $form, $user);
        }

        return $this->render('@TalavUser/profile/profile_edit.html.twig', [
            'form' => $form,
        ]);
    }

//    #[Route(path: '/view/{username}', name: 'user_profile_show', requirements: ['username' => Requirement::ASCII_SLUG])]
//    #[ParamConverter('user', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
    #[Route(path: '/{id}/{username}/{page<\d+>?1}', name: 'user_profile_view', requirements: ['username' => Requirement::ASCII_SLUG], defaults: ['username' => null])]
//    #[ParamConverter('user', class: UserInterface::class, options: ['mapping' => ['username_canonical' => 'username']])]
    public function show(int $id, string $username, $page, PaginatorService $paginator, ProfileBlockRepository $profileBlockRepository): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->userManager->getRepository()->findOneBy(['usernameCanonical' => $username]);
        $profile = $user->getProfile();

        if ($profile === null || $profile->isBlocked($currentUser->getProfile())) {
            throw $this->createNotFoundException();
        }

        // posts
        if ($user === $this->getUser() || $this->isGranted('ROLE_MODERATOR')) {
            $criteria = ['author' => $user];
        } else {
            $criteria = ['author' => $user, 'status' => true];
        }
//        if (!$defender->isGranted($this->getUser(),'ROLE_GUEST') && $user === $this->user() || $this->isGranted('ROLE_POST_MODERATOR')) {
//            $criteria = ['author' => $user];
//        } else {
//            $criteria = ['author' => $user, 'status' => true];
//        }

        $paginator
            ->setClass(PostInterface::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setCriteria($criteria)
            ->setParameters(['id' => $profile->getId(), 'username' => $user->getUsername()])
            ->setLimit(30)
            ->setPage($page)
        ;

        $photos = $this->entityManager->getRepository(GalleryImage::class)->findFreshImagesLimit(5);

        return $this->render('@TalavUser/profile/profile_view.html.twig', [
            'user' => $user,
            'profile' => $profile,
	        'photos' => $photos,
            'posts' => $paginator->getData(),
            'paginator' => $paginator,
            'type' => 'list'
        ]);

        if (null !== $username) {
            $user = $this->userManager->getRepository()->findOneBy(['usernameCanonical' => $username]);
            if (!$user instanceof \Talav\Component\User\Model\UserInterface) {
                $e = new UserNotFoundException('Пользователь "'.$username.'" не найден.');
                $e->setUserIdentifier($username);
                throw $e;
            }
        } else {
            $user = $currentUser;
        }

        return $this->render('@TalavUser/profile/profile_view.html.twig', [
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

    private function updateUser(Request $request, FormInterface $form, UserInterface $user): Response
    {
        $event = new UserFormEvent($user, $form, $request);
        $this->eventDispatcher->dispatch($event, TalavUserEvents::PROFILE_EDIT_SUCCESS);

        $this->userManager->update($user, true);

        if (null === $response = $event->getResponse()) {
            $response = new RedirectResponse($this->generateUrl('user_profile_edit'));
        }

        $this->eventDispatcher->dispatch(
            new FilterUserResponseEvent($user, $request, $response),
            TalavUserEvents::PROFILE_EDIT_COMPLETED
        );

        return $response;
    }
}
