<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;

#[AsController]
#[Route(path: '/user/relation', name: 'user_relation_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class FriendController extends AbstractController
{
	#[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_profile');
    }

	#[Route('/{user_id<\d+>}/add-as-friend', methods: Request::METHOD_PATCH)]
	public function addAsFriend(
		FriendResponseData $responseData,
		ApplyInFriend $addAsFriend
	): HttpResponseCollectorInterface {
		return $this->responseData(
			$responseData,
			$addAsFriend->process($this->getAuthorizedUser(), $friend),
			PlatformCodeEnum::UPDATED
		);
	}
}
