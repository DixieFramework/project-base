<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Form\Model\ProfileFilterFormModel;
use Talav\ProfileBundle\Form\Type\ProfileFilterFormType;
use Talav\ProfileBundle\Repository\InterestRepository;
use Talav\ProfileBundle\Service\FilterService;
use Talav\ProfileBundle\Service\UserInterestFilterService;
use Talav\WebBundle\Repository\RegionRepository;

#[AsController]
#[Route(path: '/profile/search', name: 'profile_search_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class ProfileSearchController extends AbstractController
{
	public function __construct(
		private readonly FormFactoryInterface $formFactory,
		private readonly Security $security,
		private readonly FilterService $filterService,
		private readonly InterestRepository $interestRepository,
		private readonly UserInterestFilterService $userInterestFilterService,
		private readonly RegionRepository $regionRepository,
		private readonly ManagerInterface $profileManager
	)
	{
	}

	#[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
	    /** @var UserInterface $currentUser */
	    $currentUser = $this->security->getUser();
	    $currentProfileId = $currentUser->getProfile()->getId();

	    $profile = $this->profileManager->getRepository()->find($currentProfileId);

	    $filter = $this->filterService->create($currentProfileId);
	    $userInterestFilters = $this->userInterestFilterService->findByProfileId($currentProfileId);

	    $filterForm = new ProfileFilterFormModel();
	    $filterForm->setDistance($filter->getDistance());
	    $filterForm->setMaxAge($filter->getMaxAge());
	    $filterForm->setMinAge($filter->getMinAge());
	    $filterForm->setRegion($filter->getRegion());
	    $filterForm->setInterests($userInterestFilters);

	    $filterFormType = $this->formFactory->create(
		    ProfileFilterFormType::class,
		    $filterForm,
		    [
			    'regions' => $this->regionRepository->findByCity($profile->getCity()->getId()),
			    'interests' => $this->interestRepository->findAll()
		    ]
	    );

	    $filterFormType->handleRequest($request);

	    if ($filterFormType->isSubmitted() && $filterFormType->isValid()) {
		    $this->entityManager->wrapInTransaction(function ($entityManager) use ($currentProfileId, $filter, $filterForm) {
			    $filter->setRegion($filterForm->getRegion());
			    $filter->setMinAge($filterForm->getMinAge());
			    $filter->setMaxAge($filterForm->getMaxAge());
			    $filter->setDistance($filterForm->getDistance());

			    $this->userInterestFilterService->createUserInterestFilters($currentProfileId, $filterForm->getInterests());
		    });

		    return new RedirectResponse($this->generateUrl('user_search_index'));
	    }

	    return new Response('Hello world from talav_profile');
    }
}
