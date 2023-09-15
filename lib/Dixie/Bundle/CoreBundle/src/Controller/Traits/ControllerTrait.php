<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

trait ControllerTrait
{
    public static function getSubscribedServices(): array
    {
        return [
            'logger' => '?'.LoggerInterface::class,
            'translator' => TranslatorInterface::class,
            'router' => '?'.RouterInterface::class,
            'request_stack' => '?'.RequestStack::class,
            'http_kernel' => '?'.HttpKernelInterface::class,
            'session' => '?'.SessionInterface::class,
            'security.authorization_checker' => '?'.AuthorizationCheckerInterface::class,
            'twig' => '?'.Environment::class,
            'form.factory' => '?'.FormFactoryInterface::class,
            'security.token_storage' => '?'.TokenStorageInterface::class,
            'security.csrf.token_manager' => '?'.CsrfTokenManagerInterface::class,
            'parameter_bag' => '?'.ContainerBagInterface::class,
            'controller_resolver' => 'controller_resolver',
        ];
    }

    protected function validateCsrfToken(Request $request, string $intention): void
    {
        if (!$this->container->has('security.csrf.token_manager')) {
            return;
        }

        $valid = $this->container->get('security.csrf.token_manager')
            ->isTokenValid(new CsrfToken($intention, $request->get('_sonata_csrf_token')));

        if (!$valid) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'The csrf token is not valid, CSRF attack?');
        }
    }

    final protected function getCsrfToken(string $intention): ?string
    {
        if (!$this->container->has('security.csrf.token_manager')) {
            return null;
        }

        return $this->container->get('security.csrf.token_manager')->getToken($intention)->getValue();
    }

    final protected function isXmlHttpRequest(Request $request): bool
    {
        return $request->isXmlHttpRequest()
            || $request->request->getBoolean('_xml_http_request')
            || $request->query->getBoolean('_xml_http_request');
    }

    protected function getBaseTemplate(): string
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $this
//            ->getTemplateRegistry()
            ->getTemplate($this->isXmlHttpRequest($request) ? 'ajax' : 'layout');
    }

    final protected function trans(
        string $id,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): string {
        return $this->container->get('translator')
            ->trans(
                $id,
                $parameters,
                $domain ?? $this->container->getParameter('translation_domain'), // getTranslationDomain()
                $locale
            );
    }

    final protected function renderWithExtraParams(
        string $view,
        array $parameters = [],
        ?Response $response = null
    ): Response {
        return $this->render($view, $this->addRenderExtraParams($parameters), $response);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    protected function addRenderExtraParams(array $parameters = []): array
    {
        $parameters['base_template'] ??= $this->getBaseTemplate();

        return $parameters;
    }
}
