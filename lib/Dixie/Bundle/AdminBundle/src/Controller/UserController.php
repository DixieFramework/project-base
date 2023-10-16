<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;

#[AsController]
#[Route('/admin/authentication/users', name: 'admin_auth_user_')]
final class UserController extends AbstractCrudController
{
    protected const DOMAIN = 'authentication';
    protected const ENTITY = 'user';

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepositoryInterface $userRepository): Response
    {
        return $this->queryIndex($userRepository);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function show(UserInterface $item): Response
    {
        return $this->render(
            view: $this->getViewPath('show'),
            parameters: [
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
    ], methods: ['POST'])]
    public function ban(User $item): Response
    {
        return $this->handleCommand(new BanUserCommand($item), new CrudParams(item: $item));
    }

    #[Route('/unban/{id}', name: 'unban', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['POST'])]
    public function unban(User $item): Response
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
