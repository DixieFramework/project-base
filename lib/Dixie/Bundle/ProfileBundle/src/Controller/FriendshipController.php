<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Groshy\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Interfaces\RoleInterface;
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

#[AsController]
#[Route(path: '/profile', name: 'profile_')]
#[IsGranted(RoleInterface::ROLE_USER)]
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

    #[Route('/friendship/{username}', name: 'friendship_index', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['GET', 'POST'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'usernameCanonical']])]
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
                'user' => $user,
//                'profile' => $profile,
                'selfProfile' => $user->getId()->equals($loggedInUser->getId()),
                'searchForm' => $profileSearchForm->createView(),
                'profileSearchResult' => $profileSearchResult
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('/friendship-request/create/{username}', name: 'friendship_request_create', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['POST', 'GET'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'usernameCanonical']])]
    public function createRequest(Request $request, User $user): Response
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

	        if ($request->isXmlHttpRequest()) {
		        return $this->json(['status' => 'ok']);
	        } else {
		        return $this->redirectToRoute('user_profile_view', ['id' => $requesteeProfile->getId(), 'username' => StringUtils::slug($user->getUsernameCanonical())]);
	        }
        }

        throw $this->createNotFoundException();
    }

    #[Route('/friendship-request/delete/{username}', name: 'friendship_request_delete', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['DELETE', 'GET', 'POST'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'usernameCanonical']])]
    public function deleteRequest(Request $request, User $user): Response
    {
        /** @var UserInterface $loggedInUser */
        $loggedInUser = $this->getUser();

        $friendshipRequest = $this->getFriendshipRequestIfExists($loggedInUser->getProfile()->getId(), $user->getProfile()->getId());

        if ($friendshipRequest) {
            $this->friendshipRequestRepository->delete($friendshipRequest, true);

	        if ($request->isXmlHttpRequest()) {
		        return $this->json(['status' => 'ok']);
	        } else {
		        return $this->redirectToRoute('profile_friendship_index', ['username' => StringUtils::slug($loggedInUser->getUsernameCanonical())]);
	        }
        }

        throw $this->createNotFoundException();
    }

    #[Route('/friendship/create/{username}', name: 'friendship_create', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['POST', 'GET'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'usernameCanonical']])]
    public function createFriendship(Request $request, User $user): Response
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

            $this->friendshipRequestRepository->delete($friendshipRequest, true);

            if ($request->isXmlHttpRequest()) {
                return $this->json(['username' => $friendProfile->getUser()->getUsernameCanonical()]);
            } else {
                return $this->redirectToRoute('profile_friendship_index', ['username' => StringUtils::slug($loggedInUser->getUsernameCanonical())]);
            }
//            return new JsonResponse(['username' => $friendProfile->getUser()->getUsername()]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('/friendship/delete/{username}', name: 'friendship_delete', requirements: ['username' => Requirement::ASCII_SLUG], methods: ['DELETE', 'GET', 'POST'])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['username' => 'usernameCanonical']])]
    public function deleteFriendship(Request $request, User $user): Response
    {
        /** @var UserInterface $loggedInUser */
        $loggedInUser = $this->getUser();

        $friendshipObjects = $this->friendshipRepository->findBy([
            'profile' => [$loggedInUser->getProfile()->getId(), $user->getProfile()->getId()],
            'friend' => [$loggedInUser->getProfile()->getId(), $user->getProfile()->getId()]
        ]);


        foreach ($friendshipObjects as $friendship) {
            $this->friendshipRepository->delete($friendship, true);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json(['username' => $this->profileRepository->find($user->getProfile()->getId())->getUser()->getUsernameCanonical()]);
        } else {
            return $this->redirectToRoute('profile_friendship_index', ['username' => StringUtils::slug($loggedInUser->getUsernameCanonical())]);
        }
//        return new JsonResponse(['username' => $this->profileRepository->find($user->getProfile()->getId())->getUser()->getUsername()]);
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
