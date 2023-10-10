<?php

declare(strict_types=1);

namespace Talav\UserBundle\Handler;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    public function __construct(protected TranslatorInterface $translator, protected RouterInterface $router)
    {dd($this->translator);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse|RedirectResponse
    {dd($request);
        if ($request->isXmlHttpRequest()) {
            $result = ['success' => true];

            return new JsonResponse($result);
        }

        $key = '_security.main.target_path'; // where "main" is your firewall name

        if ($targetPath = $request->getSession()->get($key)) {
            $url = $targetPath;
        } elseif ($request->getSession()->has($key)) {
            // set the url based on the link they were trying to access before being authenticated
            $url = $request->getSession()->get($key);
            // remove the session key
            $request->getSession()->remove($key);
        } else {
            $user = $token->getUser();

            $url = $this->router->generate('app_index');
        }
        $url = $this->router->generate('app_index');

        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse|RedirectResponse
    {
        dd($request);
//        if ($request->isXmlHttpRequest()) {
//            $result = [
//                'success' => false,
//                'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security'),
//            ];
//
//            return new JsonResponse($result);
//        }
//
//        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('homepage');

        return new RedirectResponse($url);
    }
}
