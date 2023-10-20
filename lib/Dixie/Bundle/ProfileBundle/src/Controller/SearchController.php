<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use App\Entity\Member;
use App\Entity\Preference;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Entity\UserPreference;
use Talav\ProfileBundle\Form\Model\SearchFormRequest;
use App\Form\MapSearchFormType;
use App\Pagerfanta\SearchAdapter;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\ProfileBundle\Form\Type\SearchFormType;
use Talav\ProfileBundle\Repository\ProfileRepository;

class SearchController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     */
    #[Route(path: '/search/members', name: 'search_members')]
    public function searchMembers(Request $request)
    {
		$options = [
			// enable/disable CSRF protection for this form
			'csrf_protection' => true,
			// the name of the hidden HTML field that stores the token
			'csrf_field_name' => '_csrf_token',
			// an arbitrary string used to generate the value of the token
			// using a different string for each form improves its security
			'csrf_token_id'   => 'authenticate',
		];

        $members = null;
        $memberSearch = $this->createFormBuilder(null, $options)
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'attr' => [
                    'class' => 'form-control bg-transparent',
                ],
                'help' => 'help.username.auto.complete',
            ])
            ->add('search', SubmitType::class, [
                'label' => false,
	            'icon' => 'fa-search fs-6',
	            'attr' => [
					'class' => 'btn bg-transparent border-0 px-2 py-0 position-absolute top-50 end-0 translate-middle-y'
	            ]
            ])
	        ->getForm()
        ;

        $memberSearch->handleRequest($request);
        if ($memberSearch->isSubmitted() && $memberSearch->isValid()) {
            $data = $memberSearch->getData();
            $username = $data['username'];
            /** @var ProfileRepository $memberRepository */
            $memberRepository = $this->entityManager->getRepository(ProfileInterface::class);
            $members = $memberRepository->findByProfileInfoStartsWith($username);
        }

        return $this->render('@TalavProfile/search/members.html.twig', [
            'form' => $memberSearch->createView(),
            'members' => $members,
        ]);
    }

    /**
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    #[Route(path: '/search/locations', name: 'search_locations')]
    public function searchLocations(Request $request, TranslatorInterface $translator, SerializerInterface $serializer)
    {
        $pager = null;
        $results = null;

        /** @var ProfileInterface $member */
        $member = $this->getUser()->getProfile();

        $preferenceRepository = $this->entityManager->getRepository(UserPreference::class);

        $showMap = $member->getPreferenceValue(UserPreference::SHOW_MAP, false);
        $showOptions = $member->getPreferenceValue(UserPreference::SHOW_SEARCH_OPTIONS, false);

	    $searchOptions = $member->getPreferenceValue(UserPreference::SEARCH_OPTIONS, '');

        if ("" !== $searchOptions) {
            $searchFormRequest = unserialize($searchOptions);
        } else {
            $searchFormRequest = new SearchFormRequest();
        }
        $searchFormRequest->overrideFromRequest($request);
        $searchFormRequest->show_map = ('Yes' === $showMap);
        $searchFormRequest->show_options = ('Yes' === $showOptions);

        // There are three different forms that might end up on this page
        $formFactory = $this->container->get('form.factory');
        $tiny = $formFactory->createNamed('tiny', SearchFormType::class, $searchFormRequest);
        $home = $formFactory->createNamed('home', SearchFormType::class, $searchFormRequest);
        /** @var FormInterface $search */
        $search = $formFactory->createNamed('', SearchFormType::class, $searchFormRequest, [
            'search_options' => $searchOptions,
        ]);

        $request = $this->overrideRequestParameters($request, $searchFormRequest);

        // Check which form was used to get here
        $tiny->handleRequest($request);
        $tinyIsSubmitted = $tiny->isSubmitted();
        $tinyIsValid = ($tinyIsSubmitted && $tiny->isValid());

        $home->handleRequest($request);
        $homeIsSubmitted = $home->isSubmitted();
        $homeIsValid = ($homeIsSubmitted && $home->isValid());

        $search->handleRequest($request);
        $searchIsSubmitted = $search->isSubmitted();
        $searchIsValid = ($searchIsSubmitted && $search->isValid());

        if ($tinyIsValid || $homeIsValid || $searchIsValid) {
            $data = null;
            $em = $this->entityManager;
            /* @var SearchFormRequest $data */
            if ($tinyIsValid) {
                $data = $tiny->getData();
            }
            if ($homeIsValid) {
                $data = $home->getData();
            }
            if ($searchIsValid) {
                $data = $search->getData();
                if ($search->has('resetOptions') && $search->get('resetOptions')->isClicked()) {
                    $em->remove($memberSearchOptionsPreference);
                    $em->flush();

                    return $this->redirectToRoute('search_locations');
                }

                // serialize the search options and store them in the preference
                $searchOptions = serialize($searchFormRequest);
                $member->setPreferenceValue(UserPreference::SEARCH_OPTIONS, $searchOptions);
                $em->persist($member);
            }

			/** @var UserPreference $memberShowMapPreference */
//            $memberShowMapPreference = $member->getPreference(UserPreference::SHOW_MAP);
//            $memberShowMapPreference->setValue($data->show_map ? 'Yes' : 'No');
//            $memberShowOptionsPreference = $member->getPreference(UserPreference::SHOW_SEARCH_OPTIONS);
//            $memberShowOptionsPreference->setValue($data->show_options ? 'Yes' : 'No');
//            $em->persist($memberShowMapPreference);
//            $em->persist($memberShowOptionsPreference);
//            $em->flush();

            $searchAdapter = new SearchAdapter(
                $data,
                $this->get('session'),
                $this->getParameter('database_host'),
                $this->getParameter('database_name'),
                $this->getParameter('database_user'),
                $this->getParameter('database_password'),
                $em,
                $translator
            );
            $results = $searchAdapter->getFullResults();
            $pager = new Pagerfanta($searchAdapter);
            $pager->setMaxPerPage($data->items);
            $pager->setCurrentPage($request->get('page', 1));
            if (!$searchIsValid) {
                // only set data if the form wasn't submitted from search_locations
                $search->setData($data);
            }
        } elseif ($tinyIsSubmitted) {
            // The user probably clicked on 'go' to fast on the landing page
            // so set the entered location into the search location field and just show the form
            $viewData = $tiny->getViewData();
            $search->get('location')->submit($viewData->location);
        }

        return $this->render('@TalavProfile/search/locations.html.twig', [
            'form' => $search->createView(),
            'pager' => $pager,
            'routeName' => 'search_members_ajax',
            'routeParams' => $request->query->all(),
            'results' => $results,
        ]);
    }

    /**
     * This method is used on the home screen to allow people interested in BeWelcome to check how many members are
     * available in a location.
     *
     *
     * @return Response|RedirectResponse
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    #[Route(path: '/search/map', name: 'search_map')]
    public function showMapAction(Request $request, TranslatorInterface $translator): Response
    {
        // do not allow access to this page when logged in, redirect to /search/locations
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('search_locations');
        }

        $results = null;

        $form = $this->createForm(MapSearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchFormRequest = new SearchFormRequest();
            $searchFormRequest->page = 1;
            $searchFormRequest->location = $data['location'];
            $searchFormRequest->location_geoname_id = $data['location_geoname_id'];
            $searchFormRequest->location_latitude = $data['location_latitude'];
            $searchFormRequest->location_longitude = $data['location_longitude'];
            $searchFormRequest->accommodation_anytime = true;
            $searchFormRequest->accommodation_neverask = true;
            $searchFormRequest->profile_picture = false;
            $searchFormRequest->about_me = false;
            $searchFormRequest->has_comments = false;
            $searchFormRequest->last_login = 2400;
            $searchFormRequest->distance = 100;

            $searchAdapter = new SearchAdapter(
                $searchFormRequest,
                $this->get('session'),
                $this->getParameter('database_host'),
                $this->getParameter('database_name'),
                $this->getParameter('database_user'),
                $this->getParameter('database_password'),
                $this->entityManager,
                $translator
            );
            $results = $searchAdapter->getMapResults();
            $pager = new Pagerfanta($searchAdapter);
            $pager->setMaxPerPage($searchFormRequest->items);
            $pager->setCurrentPage($searchFormRequest->page);
        }

        return $this->render('search/searchmap.html.twig', [
            'form' => $form->createView(),
            'map' => true,
            'results' => $results,
        ]);
    }

    /**
     *
     * @return Response
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    #[Route(path: '/search/locations/ajax', name: 'search_members_ajax')]
    public function searchGetPageResultsAjax(Request $request, TranslatorInterface $translator)
    {
        if ('POST' !== $request->getMethod()) {
            // JavaScript doesn't work on client
            // redirect to search members
            return $this->redirectToRoute('search_locations', $request->query->all());
        }

        $searchFormRequest = SearchFormRequest::fromRequest($request);

        $searchAdapter = new SearchAdapter(
            $searchFormRequest,
            $this->get('session'),
            $this->getParameter('database_host'),
            $this->getParameter('database_name'),
            $this->getParameter('database_user'),
            $this->getParameter('database_password'),
            $this->entityManager,
            $translator
        );
        $pager = new Pagerfanta($searchAdapter);
        $pager->setMaxPerPage($searchFormRequest->items);
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render('member/results.html.twig', [
            'pager' => $pager,
            'routeName' => 'search_members_ajax',
            'routeParams' => $request->query->all(),
        ]);
    }

    private function overrideRequestParameters(Request $request, SearchFormRequest $searchFormRequest)
    {
        // Override the bounding box in case of regular search,
        // if distance isn't set through Javascript on map.
        // This provides a bounding box to the JS code to zoom into the map for the results.
        if ($request->query->has('search')) {
            $parameters = $request->query->get('search');
            if ('-1' !== $parameters['distance']) {
                $parameters['ne_latitude'] = $searchFormRequest->ne_latitude;
                $parameters['ne_longitude'] = $searchFormRequest->ne_longitude;
                $parameters['sw_latitude'] = $searchFormRequest->sw_latitude;
                $parameters['sw_longitude'] = $searchFormRequest->sw_longitude;
                $request->query->set('search', $parameters);
            }
        }

        return $request;
    }
//
//	/**
//	 * Creates and returns a form builder instance.
//	 *
//	 * @final
//	 */
//	protected function createFormBuilder($data = null, array $options = []): FormBuilderInterface
//	{
//		return $this->container->get('form.factory')->createBuilder(FormType::class, $data, $options);
//	}
}
