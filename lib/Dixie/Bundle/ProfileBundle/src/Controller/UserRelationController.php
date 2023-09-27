<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Groshy\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Form\Type\UserRelationType;
use Talav\ProfileBundle\Repository\UserRelationRepository;

#[AsController]
#[Route(path: '/user/relation', name: 'user_relation_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class UserRelationController extends AbstractController
{
	/**
	 * @var int
	 */
	private const RELATIONS_PER_PAGE = 25;

	public function __construct(private readonly SluggerInterface $slugger) { }

	#[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_profile');
    }

	#[Route(path: '/add/{username}', name: 'user_profile_user_relation_add', requirements: ['username' => Requirement::ASCII_SLUG])]
	#[ParamConverter('member', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
	public function add(User $user, ManagerInterface $userRelationManager, EntityManagerInterface $em): Response
	{
		if ($this->getUser() === $user) {
			return $this->redirectToRoute('user_profile_show', ['username' => $user->getUsername()]);
		}

		$userRelationRepository = $userRelationManager->getRepository();

		$follow = $userRelationRepository->findOneBy(['owner' => $this->getUser(), 'receiver' => $user]);

		if ($follow) {
			$this->getUser()->removeSendedUserRelation($follow);
			$this->entityManager->remove($follow);
			$response = ['status' => 'removed'];
		} else {
			/** @var UserRelation $follow */
			$follow = $userRelationManager->create();
			$this->getUser()->addSendedUserRelation($follow);

			$follow->setOwner($this->getUser());
			$follow->setReceiver($user);
			$follow->setComment(1);
			$follow->setCommentText('friend');
			if (!$user->isEnabled()) {
				$follow->setConfirmed('No');
				$response = ['status' => 'requested'];
			} else {
//				$notification = new Notification();
//				$notification->setType('user_follow');
//				$notification->setReceiver($user);
//				$notification->setFollow($follow);
//				$notification->setSender($this->user());

				$follow->setConfirmed('Yes');
				$response = ['status' => 'added'];
//				$notificationRepo->persist($notification);
			}
			$this->entityManager->persist($follow);
		}

		$this->entityManager->flush();

		return $this->json([
			'response' => $response
		]);
	}

//	/**
//	 * @Route("/add/{username}", name="add_relation")
//	 */
//	#[Route(path: '/add/{username}', name: 'user_profile_user_relation_add', requirements: ['username' => Requirement::ASCII_SLUG])]
//	#[ParamConverter('member', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
//	public function add(Request $request, User $member): Response
//	{
//		/** @var UserInterface $loggedInMember */
//		$loggedInMember = $this->getUser();
//		if ($member === $loggedInMember) {
//			return $this->redirectToRoute(AbstractController::HOME_PAGE);
////			return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
//		}
//
//		$relation = $this->findRelationBetween($loggedInMember, $member);
//		if (null !== $relation) {
//			return $this->redirectToRoute('edit_relation', ['username' => $member->getUsername()]);
//		}
//
//		$form = $this->createForm(UserRelationType::class, $relation);
//
//		$form->handleRequest($request);
//		if ($form->isSubmitted() && $form->isValid()) {
//			/** @var UserRelation $relation */
//			$relation = $form->getData();
//			$relation->setOwner($loggedInMember);
//			$relation->setReceiver($member);
//
//			$this->entityManager->persist($relation);
//			$this->entityManager->flush();
//
//			//$mailer->sendRelationNotification($relation);
//
//			return $this->redirectToRoute('relations', ['username' => $loggedInMember->getUsername()]);
//		}
//
//		return $this->render('@TalavProfile/user_relation/add.html.twig', [
//			'form' => $form->createView(),
//			'member' => $member,
////			'globals_js_json' => $this->globals->getGlobalsJsAsJson($member, $loggedInMember),
////			'submenu' => $this->profileSubmenu->getSubmenu($member, $loggedInMember, ['active' => 'add_relation']),
//		]);
//	}

	/**
	 * @Route("/members/{username}/relation/edit", name="edit_relation")
	 */
	#[Route(path: '/edit/{username}', name: 'edit_relation', requirements: ['username' => Requirement::ASCII_SLUG])]
	#[ParamConverter('member', class: User::class, options: ['mapping' => ['username' => 'username']])]
	public function edit(Request $request, UserInterface $member): Response
	{
		/** @var UserInterface $loggedInMember */
		$loggedInMember = $this->getUser();
		if ($member === $loggedInMember) {
			return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
		}

		$relation = $this->findRelationBetween($loggedInMember, $member);
		if (null === $relation) {
			return $this->redirectToRoute('add_relation', ['username' => $member->getUsername()]);
		}

		$form = $this->createForm(UserRelationType::class, $relation);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$relation = $form->getData();
			$relation->setUpdated(new DateTime());

			$this->entityManager->merge($relation);
			$this->entityManager->flush();

			return $this->redirectToRoute('relations', ['username' => $loggedInMember->getUsername()]);
		}

		return $this->render('relation/edit.html.twig', [
			'form' => $form->createView(),
			'member' => $member,
			'globals_js_json' => $this->globals->getGlobalsJsAsJson($member, $loggedInMember),
			'submenu' => $this->profileSubmenu->getSubmenu($member, $loggedInMember, ['active' => 'edit_relation']),
		]);
	}

	#[Route(path: '/delete/{username}', name: 'delete_relation', requirements: ['username' => Requirement::ASCII_SLUG])]
	#[ParamConverter('member', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
	public function remove(User $member, EntityManagerInterface $entityManager): Response
	{
		/** @var UserInterface $loggedInMember */
		$loggedInMember = $this->getUser();
		if ($member === $loggedInMember) {
			return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
		}

		/** @var UserRelationRepository $relationRepository */
		$relationRepository = $this->entityManager->getRepository(UserRelation::class);

		$relation = $relationRepository->findRelationBetween($loggedInMember, $member);
		if (null === $relation) {
			return $this->redirectToRoute('user_relation_relations', ['username' => $this->slugger->slug($member->getUsername())]);
		}

		$entityManager->remove($relation);
		$entityManager->flush();

		$this->infoTrans('flash.relation.removed');

		return $this->redirectToRoute('user_relation_relations', ['username' => $this->slugger->slug($member->getUsername())]);
	}

	/**
	 * @Route("/confirm/{username}", name="confirm_relation")
	 */
	#[Route(path: '/confirm/{username}', name: 'confirm', requirements: ['username' => Requirement::ASCII_SLUG])]
	#[ParamConverter('member', class: UserInterface::class, options: ['mapping' => ['username' => 'username']])]
	public function confirm(User $member, EntityManagerInterface $entityManager): Response
	{
		/** @var UserInterface $loggedInMember */
		$loggedInMember = $this->getUser();
		if ($member === $loggedInMember) {
			return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
		}

		/** @var UserRelationRepository $relationRepository */
		$relationRepository = $this->entityManager->getRepository(UserRelation::class);

		$relation = $relationRepository->findUnconfirmedRelationBetween($member, $loggedInMember);
		if (null === $relation) {
			return $this->redirectToRoute('user_relation_relations', ['username' => $member->getUsername()]);
		}

		$relation->setConfirmed('Yes');
		$entityManager->flush();

		$this->infoTrans('flash.relation.confirmed');

		return $this->redirectToRoute('user_relation_relations', ['username' => $this->slugger->slug($member->getUsername())]);
	}

	/**
	 * @Route("/members/{username}/relation/dismiss", name="dismiss_relation")
	 */
	public function dismiss(UserInterface $member, EntityManagerInterface $entityManager): Response
	{
		/** @var UserInterface $loggedInMember */
		$loggedInMember = $this->getUser();
		if ($member === $loggedInMember) {
			return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
		}

		/** @var UserRelationRepository $relationRepository */
		$relationRepository = $this->entityManager->getRepository(UserRelation::class);
		$relation = $relationRepository->findUnconfirmedRelationBetween($member, $loggedInMember);
		if (null === $relation) {
			return $this->redirectToRoute('relations', ['username' => $member->getUsername()]);
		}

		$entityManager->remove($relation);
		$entityManager->flush();

		$this->infoTrans('flash.relation.dismissed');

		return $this->redirectToRoute('relations', ['username' => $member->getUsername()]);
	}

	/**
	 * @Route("/relations/{username}/{page}", name="relations")
	 */
	#[Route(path: '/relations/{username}/{page}', name: 'relations', requirements: ['username' => Requirement::ASCII_SLUG])]
	public function relations(User $member, int $page = 1): Response
	{
		/** @var UserInterface $loggedInMember */
		$loggedInMember = $this->getuser();

		/** @var UserRelationRepository $relationRepository */
		$relationRepository = $this->entityManager->getRepository(UserRelation::class);
		$relations = $relationRepository->getRelations($member, $page, self::RELATIONS_PER_PAGE);

		return $this->render('@TalavProfile/user_relation/relations.html.twig', [
			'member' => $member,
			'relations' => $relations,
//			'globals_js_json' => $this->globals->getGlobalsJsAsJson($member, $loggedInMember),
//			'submenu' => $this->profileSubmenu->getSubmenu($member, $loggedInMember, ['active' => 'relations']),
		]);
	}

	private function findRelationBetween(UserInterface $loggedInMember, UserInterface $member): ?UserRelation
	{
		/** @var UserRelationRepository $relationRepository */
		$relationRepository = $this->entityManager->getRepository(UserRelation::class);

		return $relationRepository->findRelationBetween($loggedInMember, $member);
	}
}
