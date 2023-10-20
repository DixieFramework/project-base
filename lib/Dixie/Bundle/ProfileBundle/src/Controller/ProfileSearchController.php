<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Repository\InterestRepository;
use Talav\ProfileBundle\Service\FilterService;
use Talav\ProfileBundle\Service\UserInterestFilterService;

#[AsController]
#[Route(path: '/profile/search', name: 'profile_search_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class ProfileSearchController extends AbstractController
{
	public function __construct(
		private readonly Security $security,
		private readonly FilterService $filterService,
		private readonly InterestRepository $interestRepository,
		private readonly UserInterestFilterService $userInterestFilterService,
	)
	{
	}

	#[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
	    /** @var UserInterface $currentUser */
	    $currentUser = $this->security->getUser();
	    $currentProfileId = $currentUser->getProfile()->getId();

	    $filter = $this->filterService->create($currentProfileId);
	    $userInterestFilters = $this->userInterestFilterService->findByProfileId($currentProfileId);



	    return new Response('Hello world from talav_profile');
    }
}
