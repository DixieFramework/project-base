<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use AutoMapperPlus\AutoMapperInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Groshy\Entity\Profile;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\Component\User\ValueObject\Roles;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Enums\Importance;
use Talav\CoreBundle\Form\User\ProfileEditType;
use Talav\CoreBundle\Form\User\UserRegistrationType;
use Talav\CoreBundle\Form\User\UserType;
use Talav\CoreBundle\Mime\RegistrationEmail;
use Talav\CoreBundle\Service\EmailVerifier;
use Talav\CoreBundle\Service\RoleBuilderService;
use Talav\CoreBundle\Service\UserExceptionService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Talav\CoreBundle\Traits\SecurityAwareTrait;
use Talav\ProfileBundle\Form\Type\ProfileFormType;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\UserBundle\Enum\RegistrationWorkflowEnum;
use Talav\UserBundle\Event\FilterUserResponseEvent;
use Talav\UserBundle\Event\FormEvent;
use Talav\UserBundle\Event\GetResponseRegistrationEvent;
use Talav\UserBundle\Event\GetResponseUserEvent;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserFormEvent;
use Talav\UserBundle\Form\Type\Workflow\RegistrationFormType;
use Talav\UserBundle\Security\UserFormAuthenticator;

/**
 * Controller to register a new user.
 */
#[AsController]
#[Route(path: '/legacy-register', name: 'talav_user_legacy_registration_')]
class LegacyRegistrationController extends AbstractController
{
	use SecurityAwareTrait;

    private const ROUTE_REGISTER = 'register';//'user_register';
    private const ROUTE_VERIFY = 'user_verify';

    public function __construct(
        private readonly EventDispatcherInterface   $eventDispatcher,
        private readonly RouterInterface            $router,
        private readonly EmailVerifier              $verifier,
		private readonly TokenStorageInterface      $tokenStorage,
        private readonly UserRepositoryInterface    $userRepository,
        private readonly UserManagerInterface       $userManager,
        private readonly ManagerInterface           $profileManager,
        private readonly UserExceptionService       $service,
        private readonly FormLoginAuthenticator     $formLoginAuthenticator,

        private readonly WorkflowInterface $registrationStateMachine,

    ) {
    }

    #[Route('/step/1', name: 'register_step_one')]
    public function stepOne(Request $request, UserPasswordHasherInterface $userPasswordHasher): ?Response
    {
        $session = $request->getSession();
        if ($session->has('user_data')) {
            $session->remove('user_data');
        }

        $user = $this->userManager->create();
        if ($this->registrationStateMachine->can($user, RegistrationWorkflowEnum::TRANSITION_TO_COMPLETE->value)) {
            return $this->redirectToRoute('talav_user_legacy_registration_register_step_two');
        }

	    $event = new GetResponseRegistrationEvent($user, $request);
	    $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_INITIALIZE);

	    if (null !== $event->getResponse()) {
		    return $event->getResponse();
	    }

        $userForm = $this->createForm(UserRegistrationType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted()) {
			if ($userForm->isValid()) {
				$this->registrationStateMachine->apply($user, RegistrationWorkflowEnum::TRANSITION_TO_ACCOUNT_FORM->value);

				$user->setPassword($userPasswordHasher->hashPassword($user, $userForm->get('plainPassword')->getData()));
				$session->set('user_data', $user);

				return $this->redirectToRoute('talav_user_legacy_registration_register_step_two');
			}

	        $event = new FormEvent($userForm, $request);
	        $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_FAILURE);

	        if (null !== $response = $event->getResponse()) {
		        return $response;
	        }
        }

        return $this->render('@TalavUser/registration/workflow/register_user.html.twig', [
            'user' => $user,
            'form' => $userForm,
        ]);
    }

    #[Route('/step/2', name: 'register_step_two')]
    public function stepTwo(
        Request $request,
        UserAuthenticatorInterface $userAuthenticator,
        UserFormAuthenticator $authenticator,
        WorkflowInterface $registrationStateMachine,
        ManagerInterface $roleManager,
        RoleBuilderService $roleBuilderService
    ): ?Response {
        $session = $request->getSession();
        /** @var UserInterface $user */
        $user = $session->get('user_data');

        if (! $user instanceof UserInterface) {
            return $this->redirectToRoute('talav_user_legacy_registration_register_step_one');
        }

        /** @var ProfileInterface $profile */
        $profile = $this->profileManager->create();

        $profileForm = $this->createForm(ProfileFormType::class, $profile);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted()) {
			if ($profileForm->isValid()) {
                $user->setRoles(Roles::developer());
				$user->setProfile($profile);

				$this->registrationStateMachine->apply($user, RegistrationWorkflowEnum::TRANSITION_TO_COMPLETE->value);

				return $this->updateUser($request, $user, $profileForm);
			}
        }

        return $this->render('@TalavUser/registration/workflow/register_profile.html.twig', [
            'user' => $user,
            'form' => $profileForm->createView(),
        ]);
    }

	private function updateUser(Request $request, UserInterface $user, FormInterface $form): Response
	{
		$event = new UserFormEvent($user, $form, $request);
		$this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_SUCCESS);

		$this->userManager->update($user, true);

		if (null === $response = $event->getResponse()) {
			$response = $this->redirectToHomePage('talav.registration.confirmed', ['%username%' => $user->getUsername()]);
		}

		$this->eventDispatcher->dispatch(
			new FilterUserResponseEvent($user, $request, $response),
			TalavUserEvents::REGISTRATION_COMPLETED
		);

		return $response;
	}

	#[Route('/confirm/{token}', name: 'confirm')]
	public function confirm(Request $request, string $token): Response
	{
		$userManager = $this->userManager;

		$user = $userManager->findUserByConfirmationToken($token);

		if (null === $user) {
			return new RedirectResponse($this->router->generate('talav_user_login'));
		}

		$user->setConfirmationToken(null);
		$user->setEnabled(true);

		$event = new GetResponseUserEvent($user, $request);
		$this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_CONFIRM);

		$userManager->update($user, true);

		if (null === $response = $event->getResponse()) {
			$url      = $this->router->generate('talav_user_registration_confirmed');
			$response = new RedirectResponse($url);
		}

		$this->eventDispatcher->dispatch(
			new FilterUserResponseEvent($user, $request, $response),
			TalavUserEvents::REGISTRATION_CONFIRMED,
		);

		return $response;
	}

	#[Route('/confirmed', name: 'confirmed')]
	public function confirmed(Request $request)
	{
		$user = $this->security->getUser();

		if (!$user instanceof UserInterface) {
			throw new AccessDeniedException('This user does not have access to this section.');
		}

		return $this->render('@TalavUser/registration/check_email.html.twig', [
			'user' => $user,
			'targetUrl' => $this->getTargetUrlFromSession($request->getSession())
		]);

		return new Response(
			$this->twig->render('@TalavUser/registration/confirmed.html.twig', [
				'user'      => $user,
				'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
			])
		);
	}

    #[Route('/check-email', name: 'check_email')]
    public function checkEmail(Request $request): Response
    {
        $session = $request->getSession();
        $email   = $session->get('talav_user_send_confirmation_email/email', '');

        if ('' === $email) {
            return new RedirectResponse($this->router->generate('talav_user_registration_register'));
        }

        $session->remove('talav_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->router->generate('talav_user_login'));
        }

        $this->render('@TalavUser/registration/check_email.html.twig', [
            'user' => $user
        ]);

           return $this->render('@TalavUser/registration/check_email.html.twig', [
                'user' => $user,
            ]);
	}

    /**
     * Display and process form to register a new user.
     */
    #[Route(path: '', name: self::ROUTE_REGISTER)]
    public function register(Request $request, AuthenticationUtils $utils): Response
    {
        $user = $this->userManager->create();
        $user->setPassword('fake');

        $event = new GetResponseRegistrationEvent($user, $request);
        $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(UserRegistrationType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
			$user->setIsNewUser(true);
            $this->userManager->update($user, true);

            try {
                $email = $this->createEmail($user);
                $this->verifier->sendEmail(self::ROUTE_VERIFY, $user, $email);

                return $this->redirectToHomePage();
            } catch (TransportExceptionInterface $e) {
                $this->service->handleException($request, $e);

                return $this->redirectToRoute(self::ROUTE_REGISTER);
            }
        }

        return $this->render('@TalavUser/registration/register.html.twig', [
            'error' => $utils->getLastAuthenticationError(),
            'form' => $form,
        ]);
    }

    /**
     * Verify the user e-mail.
     */
    #[Route(path: '/verify', name: self::ROUTE_VERIFY)]
    public function verify(Request $request): RedirectResponse
    {
        $user = $this->findUser($request);
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute(self::ROUTE_REGISTER);
        }

        try {
            $this->verifier->handleEmail($request, $user);
        } catch (VerifyEmailExceptionInterface $e) {
            $this->service->handleException($request, $e);

            return $this->redirectToRoute(self::ROUTE_REGISTER);
        }

        return $this->redirectToHomePage('registration.confirmed', ['%username%' => $user->getUserIdentifier()]);
    }

	private function getTargetUrlFromSession(SessionInterface $session): ?string
	{
		$token = $this->tokenStorage->getToken();

		if (null === $token || !\is_callable([$token, 'getProviderKey'])) {
			return null;
		}

		$key = sprintf('_security.%s.target_path', $token->getProviderKey());

		if ($session->has($key)) {
			return $session->get($key);
		}

		return null;
	}

    private function createEmail(UserInterface $user): RegistrationEmail
    {
        return (new RegistrationEmail())
            ->subject($this->trans('registration.subject'))
            ->from($this->getAddressFrom())
            ->to((string) $user->getEmail())
            ->update(Importance::MEDIUM, $this->getTranslator());
    }

    private function findUser(Request $request): ?UserInterface
    {
        if (0 !== $id = $this->getRequestString($request, 'id')) {
            return $this->userRepository->find($id);
        }

        return null;
    }
}
