<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Controller;

use Groshy\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\BanUserCommand;
use Talav\UserBundle\Message\Command\UnbanUserCommand;

#[AsController]
#[Route('/admin/authentication/users', name: 'admin_auth_user_')]
final class UserController extends AbstractCrudController
{
    protected const DOMAIN = 'auth';
    protected const ENTITY = 'user';

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepositoryInterface $userRepository): Response
    {
        return $this->queryIndex($userRepository);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function show(User $item): Response
//    public function show(\Symfony\Component\Security\Core\User\UserInterface $item): Response
    {
        return $this->render(
            view: $this->getViewPath('show'),
            parameters: [
				'data_id' => $item->getId()->__toString(),
                'data' => $item,
            ]
        );
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->handleCommand(new CreateUserCommand(), new CrudParams(
            action: CrudAction::CREATE,
            formClass: CreateUserForm::class,
        ));
    }

    #[Route('/edit/{id}', name: 'edit', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['GET', 'POST'])]
    public function edit(User $item, Request $request): Response
    {
        return $this->handleCommand(new UpdateUserCommand($item), new CrudParams(
            action: CrudAction::UPDATE,
            item: $item,
            formClass: UpdateUserForm::class,
            view: 'edit',
            hasShow: true,
            overrideView: true
        ));
    }

    #[Route('/ban/{id}', name: 'ban', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['POST', 'GET'])]
    public function ban(\Symfony\Component\Security\Core\User\UserInterface $item): Response
    {
        return $this->handleCommand(new BanUserCommand($item), new CrudParams(item: $item));
    }

    #[Route('/unban/{id}', name: 'unban', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function unban(\Symfony\Component\Security\Core\User\UserInterface $item): Response
    {
        return $this->handleCommand(new UnbanUserCommand($item), new CrudParams(item: $item));
    }

    #[Route('/email/{id}', name: 'email', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['GET', 'POST'])]
    public function email(User $item, Request $request): Response
    {
        return $this->handleCommand(new EmailUserCommand($item), new CrudParams(
            action: CrudAction::UPDATE,
            item: $item,
            formClass: EmailUserForm::class,
            view: 'email',
            hasShow: true,
            overrideView: true
        ));
    }

    #[Route('/{id}', name: 'delete', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['POST', 'DELETE'])]
    public function delete(User $item): Response
    {
        return $this->handleCommand(new DeleteUserCommand($item), new CrudParams(
            action: CrudAction::DELETE,
            item: $item,
        ));
    }
}
