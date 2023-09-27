<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Repository\UserFriendRepository;
use Talav\WebBundle\Service\PaginationService;

#[AsController]
#[Route(path: '/user/friend', name: 'user_friend_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class FriendController extends AbstractController
{
    public function __construct(private readonly UserManagerInterface $userManager, private readonly ManagerInterface $userFriendManager) { }

    #[Route(path: '/friends/list/{username}', name: 'wapinet_user_friends', requirements: ['username' => '.+'])]
    public function indexAction(Request $request, string $username, PaginationService $paginate): Response
    {
        $userRepository = $this->userManager->getRepository();
        /** @var UserFriendRepository $friendRepository */
        $friendRepository = $this->userFriendManager->getRepository();

        /** @var UserInterface|null $user */
        $user = $userRepository->findOneBy(['username' => $username]);
        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $page = $request->get('page', 1);

        $friends = $friendRepository->getFriendsQuery($user);
        $pagerfanta = $paginate->paginate($friends, $page);

        return $this->render('@TalavProfile/friend/index.html.twig', [
            'pagerfanta' => $pagerfanta,
            'user' => $user,
        ]);
    }

    #[Route(path: '/friends/add/{username}', name: 'wapinet_user_friends_add', requirements: ['username' => '.+'])]
    public function addAction(string $username, UserRepository $userRepository, UserFriendRepository $friendRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): RedirectResponse
    {
        /** @var UserInterface|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        /** @var UserInterface|null $friend */
        $friend = $userRepository->findOneBy(['username' => $username]);
        if (!$friend) {
            throw $this->createNotFoundException('Пользователь не найден.');
        }

        $objFriend = $friendRepository->getFriend($user, $friend);
        if (null !== $objFriend) {
            throw new \LogicException($user->getUsername().' уже в друзьях.');
        }

        $objFriend = new UserFriend();
        $objFriend->setUser($user);
        $objFriend->setFriend($friend);

        $user->getFriends()->add($objFriend);
        $entityManager->persist($user);
        $entityManager->flush();

        $messageBus->dispatch(new FriendAddMessage($user->getId(), $friend->getId()));

        return $this->redirectToRoute('wapinet_user_profile', ['username' => $friend->getUsername()]);
    }

    #[Route(path: '/friends/delete/{username}', name: 'wapinet_user_friends_delete', requirements: ['username' => '.+'])]
    public function deleteAction(string $username, UserRepository $userRepository, UserFriendRepository $friendRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): RedirectResponse
    {
        /** @var UserInterface|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        /** @var UserInterface|null $friend */
        $friend = $userRepository->findOneBy(['username' => $username]);
        if (!$friend) {
            throw $this->createNotFoundException('Пользователь не найден.');
        }

        $objFriend = $friendRepository->getFriend($user, $friend);
        if (null === $objFriend) {
            throw new \LogicException($user->getUsername().' не в друзьях.');
        }

        $user->getFriends()->removeElement($objFriend);
        $entityManager->persist($user);
        $entityManager->flush();

        $messageBus->dispatch(new FriendDeleteMessage($user->getId(), $friend->getId()));

        return $this->redirectToRoute('wapinet_user_profile', ['username' => $friend->getUsername()]);
    }
}
