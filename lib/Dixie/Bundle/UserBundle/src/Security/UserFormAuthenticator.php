<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Utils\Type;

class UserFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * @var string
     */
    private const LOGIN_ROUTE = 'talav_user_login';

    private $options;
    private $httpKernel;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly HttpUtils $httpUtils,
    ) {
        $this->options = [
            'username_parameter' => 'talav_type_user_user_login[username]',
            'password_parameter' => 'talav_type_user_user_login[password]',
            'check_path' => '/login',
            'login_path' => '/login',
            'post_only' => true,
            'form_only' => false,
            'use_forward' => false,
            'enable_csrf' => false,
            'csrf_parameter' => 'talav_type_user_user_login[_csrf_token]',
            'csrf_token_id' => 'authenticate',
        ];
    }

    #[Required]
    public function setHttpKernel(HttpKernelInterface $httpKernel): void
    {
        $this->httpKernel = $httpKernel;
    }

    /**
     * Manual login
     */
    public function login(UserInterface $user, Request $request): ?Response
    {
        return $this->userAuthenticator->authenticateUser(
            $user,
            $this,
            $request
        );
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $username = Type::string($credentials['username']);
        $password = Type::string($credentials['password']);
        $csrfToken = Type::string($credentials['csrf_token']);
//        $username = Type::string($request->request->get('talav_type_user_user_login[username]', ''));
//        $password = Type::string($request->request->get('_password', ''));
//        $csrfToken = Type::string($request->request->get('_csrf_token', ''));

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }
//    public function authenticate(Request $request): Passport
//    {
//        $email = $request->request->get('_username', '');
//
//        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
////dump($request->request->ge('_csrf_token', ''));
//        return new Passport(
//            new UserBadge($email),
//            new PasswordCredentials($request->request->get('_password', '')),
//            [
//                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
//                new RememberMeBadge(),
//            ]
//        );
//    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('groshy_frontend_dashboard_dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return parent::onAuthenticationFailure($request, $exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [];
        $credentials['csrf_token'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['csrf_parameter']);

        if ($this->options['post_only']) {
            $credentials['username'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['password_parameter']) ?? '';
        } else {
            $credentials['username'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['password_parameter']) ?? '';
        }

        if (!\is_string($credentials['username']) && (!\is_object($credentials['username']) || !method_exists($credentials['username'], '__toString'))) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be a string, "%s" given.', $this->options['username_parameter'], \gettype($credentials['username'])));
        }

        $credentials['username'] = trim($credentials['username']);

        if (\strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if (!$this->options['use_forward']) {
            return parent::start($request, $authException);
        }

        $subRequest = $this->httpUtils->createRequest($request, $this->options['login_path']);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        if (200 === $response->getStatusCode()) {
            $response->setStatusCode(401);
        }

        return $response;
    }
}
