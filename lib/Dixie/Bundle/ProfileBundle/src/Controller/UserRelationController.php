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
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Form\Type\UserRelationType;
use Talav\ProfileBundle\Repository\UserRelationRepository;
use Talav\UserBundle\Mailer\UserMailerInterface;

#[AsController]
#[Route(path: '/user-relation')]
#[IsGranted(RoleInterface::ROLE_USER)]
class UserRelationController extends AbstractController
{
    /**
     * @var int
     */
    private const RELATIONS_PER_PAGE = 25;

    #[Route(path: '/add/{username}', name: 'user_profile_user_relation_add', requirements: ['username' => Requirement::ASCII_SLUG])]
    #[ParamConverter('member', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function add(Request $request, User $user, UserMailerInterface $mailer): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
        if ($user === $loggedInMember) {
            return $this->redirectToRoute('user_profile_edit');
            //return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
        }

        $relation = $this->findRelationBetween($loggedInMember, $user);
        if (null !== $relation) {
            return $this->redirectToRoute('user_profile_user_relation_edit', ['username' => $user->getUsername()]);
        }

        $form = $this->createForm(UserRelationType::class, $relation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRelation $relation */
            $relation = $form->getData();
            $relation->setOwner($loggedInMember);
            $relation->setReceiver($user);

            $this->entityManager->persist($relation);
            $this->entityManager->flush();

            $mailer->sendRelationNotification($relation);

            return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $loggedInMember->getUsername()]);
        }

        return $this->render('@TalavProfile/user_relation/add.html.twig', [
            'form' => $form->createView(),
            'member' => $user,
//            'submenu' => $this->profileSubmenu->getSubmenu($user, $loggedInMember, ['active' => 'add_relation']),
        ]);
    }

    #[Route(path: '/edit/{username}', name: 'user_profile_user_relation_edit', requirements: ['username' => Requirement::ASCII_SLUG])]
    #[ParamConverter('member', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function edit(Request $request, User $member): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
        if ($member === $loggedInMember) {
            return $this->redirectToRoute('user_profile_edit');
//            return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
        }

        $relation = $this->findRelationBetween($loggedInMember, $member);
        if (null === $relation) {
            return $this->redirectToRoute('user_profile_user_relation_add', ['username' => $member->getUsername()]);
        }

        $form = $this->createForm(UserRelationType::class, $relation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $relation = $form->getData();
            $relation->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($relation);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $loggedInMember->getUsername()]);
        }

        return $this->render('@TalavProfile/user_relation/edit.html.twig', [
            'form' => $form->createView(),
            'member' => $member,
        ]);
    }

    #[Route(path: '/delete/{username}', name: 'user_profile_user_relation_delete', requirements: ['username' => Requirement::ASCII_SLUG])]
    #[ParamConverter('member', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function remove(User $member, EntityManagerInterface $entityManager): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
        if ($member === $loggedInMember) {
            return $this->redirectToRoute('user_profile_edit');
//            return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
        }

        /** @var UserRelationRepository $relationRepository */
        $relationRepository = $this->entityManager->getRepository(UserRelation::class);

        $relation = $relationRepository->findRelationBetween($loggedInMember, $member);
        if (null === $relation) {
            return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
        }

        $entityManager->remove($relation);
        $entityManager->flush();

        $this->infoTrans('flash.relation.removed');
//        $this->addTranslatedFlash('notice', 'flash.relation.removed');

        return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
    }

    #[Route(path: '/confirm/{username}', name: 'user_profile_user_relation_confirm', requirements: ['username' => Requirement::ASCII_SLUG])]
    #[ParamConverter('member', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function confirm(User $member, EntityManagerInterface $entityManager): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
        if ($member === $loggedInMember) {
            return $this->redirectToRoute('user_profile_edit');
//            return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
        }

        /** @var UserRelationRepository $relationRepository */
        $relationRepository = $this->entityManager->getRepository(UserRelation::class);

        $relation = $relationRepository->findUnconfirmedRelationBetween($member, $loggedInMember);
        if (null === $relation) {
            return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
        }

        $relation->setConfirmed('Yes');
        $entityManager->flush();

        $this->infoTrans('flash.relation.confirmed');
//        $this->addTranslatedFlash('notice', 'flash.relation.confirmed');

        return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
    }

    /**
     * @Route("/members/{username}/relation/dismiss", name="dismiss_relation")
     */
    public function dismiss(User $member, EntityManagerInterface $entityManager): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getUser();
        if ($member === $loggedInMember) {
            return $this->redirectToRoute('user_profile_edit');
//            return $this->redirectToRoute('members_profile', ['username' => $loggedInMember->getusername()]);
        }

        /** @var UserRelationRepository $relationRepository */
        $relationRepository = $this->entityManager->getRepository(UserRelation::class);
        $relation = $relationRepository->findUnconfirmedRelationBetween($member, $loggedInMember);
        if (null === $relation) {
            return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
        }

        $entityManager->remove($relation);
        $entityManager->flush();

        $this->infoTrans('flash.relation.dismissed');
//        $this->addTranslatedFlash('notice', 'flash.relation.dismissed');

        return $this->redirectToRoute('user_profile_user_relation_relations', ['username' => $member->getUsername()]);
    }

    #[Route(path: '/relations/{username}/{page}', name: 'user_profile_user_relation_relations', requirements: ['username' => Requirement::ASCII_SLUG])]
    public function relations(User $member, int $page = 1): Response
    {
        /** @var UserInterface $loggedInMember */
        $loggedInMember = $this->getuser();

        /** @var UserRelationRepository $relationRepository */
        $relationRepository = $this->entityManager->getRepository(UserRelation::class);
        $relations = $relationRepository->getRelations($member, $page, self::RELATIONS_PER_PAGE);

        return $this->render('@TalavProfile/user_relation/relations.html.twig', [
            'member' => $member,
            'relations' => $relations
        ]);
    }

    private function findRelationBetween(UserInterface $loggedInMember, UserInterface $member): ?UserRelation
    {
        /** @var UserRelationRepository $relationRepository */
        $relationRepository = $this->entityManager->getRepository(UserRelation::class);

        return $relationRepository->findRelationBetween($loggedInMember, $member);
    }
}
