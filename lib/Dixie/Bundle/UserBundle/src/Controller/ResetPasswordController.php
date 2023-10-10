<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Enums\Importance;
use Talav\CoreBundle\Form\User\RequestChangePasswordType;
use Talav\CoreBundle\Form\User\ResetChangePasswordType;
use Talav\CoreBundle\Mime\ResetPasswordEmail;
use Talav\CoreBundle\Service\UserExceptionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Controller to reset the user password.
 */
#[AsController]
#[Route(path: '/reset-password')]
#[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private const ROUTE_CHECK = 'app_check_email';
    private const ROUTE_FORGET = 'talav_user_reset_request';//'app_forgot_password_request';
    private const ROUTE_RESET = 'app_reset_password';
    private const THROTTLE_MINUTES = 5;
    private const THROTTLE_OFFSET = 'PT3300S';

    /**
     * Constructor.
     */
    public function __construct(
        private readonly ResetPasswordHelperInterface $helper,
        private readonly UserRepositoryInterface      $userRepository,
        private readonly UserExceptionService         $service,
        private readonly Security                     $security
    ) {
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route(path: '/check-email', name: self::ROUTE_CHECK)]
    public function checkEmail(): Response
    {
        if (!($resetToken = $this->getTokenObjectFromSession()) instanceof ResetPasswordToken) {
            $resetToken = $this->helper->generateFakeResetToken();
        }

        return $this->render('@TalavUser/reset_password/check_email.html.twig', [
            'expires_date' => $resetToken->getExpiresAt(),
            'expires_life_time' => $this->getExpiresLifeTime($resetToken),
            'throttle_date' => $this->getThrottleAt($resetToken),
            'throttle_life_time' => $this->getThrottleLifeTime(),
        ]);
    }

    /**
     * Display and process form to request a password reset.
     */
    #[Route(path: '', name: self::ROUTE_FORGET)]
    public function request(Request $request, MailerInterface $mailer, AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(RequestChangePasswordType::class);
        if ($this->handleRequestForm($request, $form)) {
            $usernameOrEmail = (string) $form->get('user')->getData();

            return $this->sendEmail($request, $usernameOrEmail, $mailer);
        }

        return $this->render('@TalavUser/reset_password/request.html.twig', [
            'error' => $utils->getLastAuthenticationError(),
            'form' => $form,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route(path: '/reset/{token}', name: self::ROUTE_RESET)]
    public function reset(Request $request, string $token = null): Response
    {
        if (null !== $token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute(self::ROUTE_RESET);
        }
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException($this->trans('reset_not_found_password_token', [], 'security'));
        }

        try {
            /** @var UserInterface $user */
            $user = $this->helper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->service->handleException($request, $e);

            return $this->redirectToRoute(self::ROUTE_FORGET);
        }

        $form = $this->createForm(ResetChangePasswordType::class, $user);
        if ($this->handleRequestForm($request, $form)) {
            $this->helper->removeResetRequest($token);
            $this->userRepository->flush();
            $this->cleanSessionAfterReset();

            return $this->redirectAfterReset($user);
        }

        return $this->render('@TalavUser/reset_password/reset.html.twig', [
            'form' => $form,
        ]);
    }

    private function createEmail(UserInterface $user, ResetPasswordToken $resetToken): ResetPasswordEmail
    {
        $context = [
            'token' => $resetToken->getToken(),
            'username' => $user->getUserIdentifier(),
            'expires_date' => $resetToken->getExpiresAt(),
            'expires_life_time' => $this->getExpiresLifeTime($resetToken),
            'throttle_date' => $this->getThrottleAt($resetToken),
            'throttle_life_time' => $this->getThrottleLifeTime(),
        ];

        return (new ResetPasswordEmail())
            ->to($user->getEmail())
            ->from($this->getAddressFrom())
            ->subject($this->trans('resetting.request.title'))
            ->update(Importance::HIGH, $this->getTranslator())
            ->action($this->trans('resetting.request.submit'), $this->getResetAction($resetToken))
            ->context($context);
    }

    private function getExpiresLifeTime(ResetPasswordToken $resetToken): string
    {
        return $this->trans(
            $resetToken->getExpirationMessageKey(),
            $resetToken->getExpirationMessageData(),
            'ResetPasswordBundle'
        );
    }

    private function getResetAction(ResetPasswordToken $resetToken): string
    {
        return $this->generateUrl(self::ROUTE_RESET, ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function getThrottleAt(ResetPasswordToken $resetToken): \DateTimeInterface
    {
        /** @var \DateTime $expireAt */
        $expireAt = clone $resetToken->getExpiresAt();
        $interval = new \DateInterval(self::THROTTLE_OFFSET);

        return $expireAt->sub($interval);
    }

    private function getThrottleLifeTime(): string
    {
        return $this->trans('%count% minute|%count% minutes', ['%count%' => self::THROTTLE_MINUTES], 'ResetPasswordBundle');
    }

    private function redirectAfterReset(UserInterface $user): Response
    {
        try {
            if (($response = $this->security->login($user, 'form_login')) instanceof Response) {
                return $response;
            }
        } catch (\Exception) {
            // ignore
        }

        return $this->redirectToHomePage('resetting.success', ['%username%' => $user->getUserIdentifier()]);
    }

    /**
     * Send email to the user for resetting the password.
     */
    private function sendEmail(Request $request, string $usernameOrEmail, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->userRepository->findByUsernameOrEmail($usernameOrEmail);
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute(self::ROUTE_CHECK);
        }

        try {
            $resetToken = $this->helper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->service->handleException($request, $e);

            return $this->redirectToRoute(self::ROUTE_FORGET);
        }

        try {
            $notification = $this->createEmail($user, $resetToken);
            $mailer->send($notification);
            $this->setTokenObjectInSession($resetToken);
        } catch (TransportExceptionInterface $e) {
            $this->helper->removeResetRequest($resetToken->getToken());
            $this->service->handleException($request, $e);

            return $this->redirectToRoute(self::ROUTE_FORGET);
        }

        return $this->redirectToRoute(self::ROUTE_CHECK);
    }
}
