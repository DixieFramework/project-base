<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use AutoMapperPlus\AutoMapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Message\Command\CreateUserCommand;
use Talav\Component\User\Message\Dto\CreateUserDto;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Util\TokenGeneratorInterface;
use Talav\CoreBundle\Form\User\UserRegistrationType;
use Talav\CoreBundle\Traits\SecurityAwareTrait;
use Talav\UserBundle\Event\FilterUserResponseEvent;
use Talav\UserBundle\Event\FormEvent;
use Talav\UserBundle\Event\GetResponseRegistrationEvent;
use Talav\UserBundle\Event\GetResponseUserEvent;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserEvent;
use Talav\UserBundle\Event\UserFormEvent;
use Talav\UserBundle\Form\Type\RegistrationFormType;
use Talav\UserBundle\Message\Event\UserRegisteredEvent;

class RegistrationController extends AbstractController
{
    use SecurityAwareTrait;

    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $formLoginAuthenticator,
        private EventDispatcherInterface $eventDispatcher,
        private AutoMapperInterface $mapper,
        private MessageBusInterface $bus,
        private readonly UserManagerInterface       $userManager,
        private readonly TokenStorageInterface      $tokenStorage,
        private readonly TokenGeneratorInterface    $tokenGenerator,
        private readonly RouterInterface            $router,
        private readonly FormFactoryInterface $formFactory,
        private iterable $parameters
    ) {
    }

    /**
     * @Route("/register", name="talav_user_register")
     */
    public function register(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->userManager->create();

        $event = new GetResponseRegistrationEvent($user, $request);
        $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createUserForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (null === $user->getConfirmationToken()) {
                    $user->setConfirmationToken($this->tokenGenerator->generateToken());
                }

                return $this->updateUser($request, $user, $form);
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_FAILURE);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@TalavUser/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);

//        $form = $this->createForm($this->parameters['form_type'], new $this->parameters['form_model'](), ['validation_groups' => $this->parameters['form_validation_groups']]);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted()) {
//            if ($form->isValid()) {
//                $dto = $this->mapper->map($form->getData(), CreateUserDto::class);
//                $user = $this->bus->dispatch(new CreateUserCommand($dto))->last(HandledStamp::class)->getResult();
//
//                $this->eventDispatcher->dispatch(new UserEvent($user), TalavUserEvents::REGISTRATION_COMPLETED);
//
//                return $this->userAuthenticator->authenticateUser(
//                    $user,
//                    $this->formLoginAuthenticator,
//                    $request
//                );
//            }
//        }
//
//        return $this->render('@TalavUser/registration/register.html.twig', [
//            'form' => $form->createView(),
//        ]);
    }
    #[Route('/confirm/{token}', name: 'talav_user_registration_confirm')]
    public function confirm(Request $request, string $token): Response
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->router->generate('talav_user_login'));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setVerified(true);

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

    #[Route('/confirmed', name: 'talav_user_registration_confirmed')]
    public function confirmed(Request $request)
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@TalavUser/registration/confirmed.html.twig', [
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

    #[Route('/check-email', name: 'talav_user_registration_check_email')]
    public function checkEmail(Request $request): Response
    {
        $session = $request->getSession();
        $email   = $session->get('talav_user_send_confirmation_email/email', '');

        if ('' === $email) {
            return new RedirectResponse($this->router->generate('talav_user_register'));
        }

        $session->remove('talav_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->router->generate('talav_user_login'));
        }

        return $this->render('@TalavUser/registration/check_email.html.twig', [
            'user' => $user,
        ]);
    }

    private function updateUser(Request $request, UserInterface $user, FormInterface $form): Response
    {
        $event = new UserFormEvent($user, $form, $request);
        $this->eventDispatcher->dispatch($event, TalavUserEvents::REGISTRATION_SUCCESS);

//        $this->eventDispatcher->dispatch(new UserRegisteredEvent($user));

        $this->userManager->update($user, true);

        if (null === $response = $event->getResponse()) {
            $response = new RedirectResponse($this->router->generate('talav_user_registration_confirmed'));
        }

        $this->eventDispatcher->dispatch(
            new FilterUserResponseEvent($user, $request, $response),
            TalavUserEvents::REGISTRATION_COMPLETED
        );

        return $response;
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

    protected function createUserForm(UserInterface $user): FormInterface
    {
        return $this->formFactory
            ->create(UserRegistrationType::class, $user, [
                'validation_groups' => ['Registration', 'User', 'Default'],
            ])
            ->add('save', SubmitType::class, [
                'label'  => 'registration.submit',
            ])
            ;
    }
}
