<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Symfony\Component\Routing\Requirement\Requirement;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Traits\CommandBusAwareDispatchTrait;
use Talav\UserBundle\Message\Command\RequestLoginLinkCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\UserBundle\Form\RequestLoginLinkForm;

#[AsController]
#[Route('/user', name: 'talav_user_')]
final class UserController extends AbstractController
{
	use CommandBusAwareDispatchTrait;
//
//
//    #[Route('/ban/{id}', name: 'ban', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
//    public function ban(UserInterface $item): Response
//    {
//        return $this->handleCommand(new BanUserCommand($item), new CrudParams(item: $item));
//    }
//
//    #[Route('/unban/{id}', name: 'unban', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
//    public function unban(UserInterface $item): Response
//    {
//        return $this->handleCommand(new UnbanUserCommand($item), new CrudParams(item: $item));
//    }
}
