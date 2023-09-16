<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Enums\Importance;
use Talav\CoreBundle\Form\User\UserRegistrationType;
use Talav\CoreBundle\Mime\RegistrationEmail;
use Talav\CoreBundle\Service\EmailVerifier;
use Talav\CoreBundle\Service\UserExceptionService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Controller to register a new user.
 */
#[AsController]
#[Route(path: '/register')]
class RegistrationController extends AbstractController
{
    private const ROUTE_REGISTER = 'talav_user_register';//'user_register';
    private const ROUTE_VERIFY = 'user_verify';

    public function __construct(
        private readonly EmailVerifier           $verifier,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserManagerInterface    $userManager,
        private readonly UserExceptionService    $service
    ) {
    }

    /**
     * Display and process form to register a new user.
     */
    #[Route(path: '', name: self::ROUTE_REGISTER)]
    public function register(Request $request, AuthenticationUtils $utils): Response
    {
        $user = $this->userManager->create();
        $user->setPassword('fake');
        $form = $this->createForm(UserRegistrationType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
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
        if (0 !== $id = $this->getRequestInt($request, 'id')) {
            return $this->userRepository->find($id);
        }

        return null;
    }
}
