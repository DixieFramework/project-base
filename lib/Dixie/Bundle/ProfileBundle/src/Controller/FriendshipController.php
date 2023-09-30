<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Groshy\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Requirement\Requirement;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Utils\StringUtils;
use Talav\ProfileBundle\Entity\Friendship;
use Talav\ProfileBundle\Entity\FriendshipRequest;
use Talav\ProfileBundle\Model\ProfileInterface;
use Talav\WebBundle\Form\Type\SearchFormType;
use Talav\ProfileBundle\Repository\FriendshipRepository;
use Talav\ProfileBundle\Repository\FriendshipRequestRepository;
use Talav\ProfileBundle\Repository\ProfileRepository;
use Talav\WebBundle\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
	private RepositoryInterface $profileRepository;
	private FriendshipRepository $friendshipRepository;
	private FriendshipRequestRepository $friendshipRequestRepository;

    public function __construct(
	    private readonly ManagerInterface            $profileManager,
	    private readonly ManagerInterface            $friendshipManager,
	    private readonly ManagerInterface            $friendshipRequestManager,
//
//        private readonly ProfileRepository           $profileRepository,
//        private readonly FriendshipRequestRepository $friendshipRequestRepository,
//        private readonly FriendshipRepository        $friendshipRepository,
//        private readonly SearchService               $searchService
    )
    {
	    $this->profileRepository = $profileManager->getRepository();
	    $this->friendshipRepository = $friendshipManager->getRepository();
	    $this->friendshipRequestRepository = $friendshipRequestManager->getRepository();
    }

    #[Route('friends/{username}', name: 'friendship_index', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['GET', 'POST'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function index(Request $request, User $user): Response
    {
        /** @var UserInterface $loggedInUser */
        $loggedInUser = $this->getUser();

        $profile = $user->getProfile();
        if ($profile/* && $profile->getPrivacySettings()->isFriendListViewAllowed($loggedInUser->getProfile())*/) {
            $profileSearchForm = $this->createForm(SearchFormType::class);
            $profileSearchForm->handleRequest($request);

            $profileSearchResult = null;
            if ($profileSearchForm->isSubmitted() && $profileSearchForm->isValid()) {
                $profileSearchResult = $this->searchService->searchProfiles(
                    $profileSearchForm->get('search_string')->getData()
                );
            }

            return $this->render('@TalavProfile/friendship/index.html.twig', [
                'profile' => $profile,
                'selfProfile' => $user->getId()->equals($loggedInUser->getId()),
                'searchForm' => $profileSearchForm->createView(),
                'profileSearchResult' => $profileSearchResult
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship-request/create/{username}', name: 'friendship_request_create', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['POST', 'GET'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function createRequest(User $user): Response
    {
        /** @var UserInterface $loggedInUser */
        $loggedInUser = $this->getUser();

        $requesterProfile = $this->profileRepository->find($loggedInUser->getProfile()->getId());
        $requesteeProfile = $user->getProfile();//$this->profileRepository->find($profileId);

        if ($requesterProfile && $requesteeProfile &&
            !$this->getFriendshipRequestIfExists($loggedInUser->getProfile()->getId(), $user->getProfile()->getId())) {
            $friendshipRequest = new FriendshipRequest();
            $friendshipRequest->setRequester($requesterProfile);
            $friendshipRequest->setRequestee($requesteeProfile);

            $this->friendshipRequestRepository->save($friendshipRequest, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship-request/delete/{profileId}', name: 'friendship_request_delete', methods: ['DELETE', 'GET'])]
    public function deleteRequest(Request $request, int $profileId): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $friendshipRequest = $this->getFriendshipRequestIfExists($user->getProfile()->getId(), $profileId);

        if ($friendshipRequest) {
            $this->friendshipRequestRepository->remove($friendshipRequest, true);

	        if ($request->isXmlHttpRequest()) {
		        return $this->json(['status' => 'ok']);
	        } else {
		        return $this->redirectToRoute('friendship_index', ['username' => StringUtils::slug($user->getUsername())]);
	        }
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship/create/{username}', name: 'friendship_create', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['POST', 'GET'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function createFriendship(User $user): Response
    {
        /** @var UserInterface $loggedInUser */
        $loggedInUser = $this->getUser();

        $friendProfile = $user->getProfile();//$this->profileRepository->find($user);

        $friendshipRequest = $this->getFriendshipRequestIfExists($loggedInUser->getProfile()->getId(), $user->getProfile()->getId());

        if ($loggedInUser->getProfile() && $friendProfile && $friendshipRequest) {

            $friendshipObjectForFirstUser = new Friendship();
            $friendshipObjectForFirstUser->setProfile($loggedInUser->getProfile());
            $friendshipObjectForFirstUser->setFriend($friendProfile);

            $friendshipObjectForSecondUser = new Friendship();
            $friendshipObjectForSecondUser->setProfile($friendProfile);
            $friendshipObjectForSecondUser->setFriend($loggedInUser->getProfile());

            $this->friendshipRepository->save($friendshipObjectForFirstUser);
            $this->friendshipRepository->save($friendshipObjectForSecondUser);

            $this->friendshipRequestRepository->remove($friendshipRequest, true);

            return new JsonResponse(['username' => $friendProfile->getUser()->getUsername()]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship/delete/{username}', name: 'friendship_delete', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['DELETE'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'username']])]
    public function deleteFriendship(int $profileId): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $friendshipObjects = $this->friendshipRepository->findBy([
            'profile' => [$user->getProfile()->getId(), $profileId],
            'friend' => [$user->getProfile()->getId(), $profileId]
        ]);


        foreach ($friendshipObjects as $friendship) {
            $this->friendshipRepository->remove($friendship, true);
        }

        return new JsonResponse(['username' => $this->profileRepository->find($profileId)->getUsername()]);
    }

    /**
     * Get friendship request made by $firstProfileId to $secondProfileId or vice versa.
     * Return null if such request does not exist.
     *
     * @param int $firstProfileId
     * @param int $secondProfileId
     * @return FriendshipRequest|null
     */
    protected function getFriendshipRequestIfExists(int $firstProfileId, int $secondProfileId): ?FriendshipRequest
    {
        return $this->friendshipRequestRepository->findOneBy([
            'requestee' => [$firstProfileId, $secondProfileId],
            'requester' => [$firstProfileId, $secondProfileId]
        ]);
    }
}
