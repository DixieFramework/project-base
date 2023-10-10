<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Enums\FlashType;
use Talav\CoreBundle\Form\FormHelper;
use Talav\CoreBundle\Traits\EntityManagerAwareTrait;
use Talav\CoreBundle\Traits\ExceptionContextTrait;
use Talav\CoreBundle\Traits\PaginateTrait;
use Talav\CoreBundle\Traits\RequestTrait;
use Talav\CoreBundle\Traits\TranslatorFlashMessageAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides common features needed in controllers.
 */
abstract class AbstractController extends BaseController
{
    use ExceptionContextTrait;
    use RequestTrait;
    use TranslatorFlashMessageAwareTrait;

    use EntityManagerAwareTrait;
    use PaginateTrait;

    /**
     * The home route name.
     */
    final public const HOME_PAGE = 'homepage';

    // services
    private ?TranslatorInterface $translator = null;

    /**
     * Gets the address from (email and name) used to send email.
     */
    public function getAddressFrom(): Address
    {
        $email = $this->getParameterString('mailer_user_email');
        $name = $this->getParameterString('mailer_user_name');

        return new Address($email, $name);
    }

    /**
     * Gets the application name and version.
     */
    public function getApplicationName(): string
    {
        $name = $this->getParameterString('app_name');
        $version = $this->getParameterString('app_version');

        return \sprintf('%s v%s', $name, $version);
    }

    /**
     * Gets the application owner.
     */
    public function getApplicationOwner(): string
    {
        return $this->getParameterString('app_owner');
    }

    /**
     * Gets the application owner URL.
     */
    public function getApplicationOwnerUrl(): string
    {
        return $this->getParameterString('app_owner_url');
    }

    public function getRequestStack(): RequestStack
    {
        if (!$this->requestStack instanceof RequestStack) {
            /* @noinspection PhpUnhandledExceptionInspection */
            $this->requestStack = $this->container->get('request_stack');
        }

        return $this->requestStack;
    }

    public static function getSubscribedServices(): array
    {
        return \array_merge(parent::getSubscribedServices(), [
            'translator' => '?'.TranslatorInterface::class,
            TranslatorInterface::class => '?'.TranslatorInterface::class,
            'validator' => '?'.ValidatorInterface::class,
            ValidatorInterface::class => '?'.ValidatorInterface::class,
            'kernel' => '?'.KernelInterface::class,
            KernelInterface::class => '?'.KernelInterface::class,
            'event_dispatcher' => '?'.EventDispatcherInterface::class,
            EventDispatcherInterface::class => '?'.EventDispatcherInterface::class,
            'messenger.default_bus' => '?' . MessageBusInterface::class,
            MessageBusInterface::class => '?' . MessageBusInterface::class,
            //'knp_paginator' => '?' . PaginatorInterface::class,
	        'logger' => '?'.LoggerInterface::class
        ]);
    }

    /**
     * Gets the translator.
     */
    public function getTranslator(): TranslatorInterface
    {
        if (!$this->translator instanceof TranslatorInterface) {
            /* @noinspection PhpUnhandledExceptionInspection */
            $this->translator = $this->container->get(TranslatorInterface::class);
        }

        return $this->translator;
    }

    public function getCommandBus(): MessageBusInterface
    {
        return $this->container->get('messenger.default_bus');
    }

	protected function dispatchEvent(object $event): object
	{
		return $this->container->get('event_dispatcher')->dispatch($event);
	}

	protected function getLogger(): LoggerInterface
	{
		return $this->container->get('logger');
	}

    protected function getCurrentRequest(): Request
    {
        $request = $this->getRequestStack()->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException("unable to get the current request");
        }

        return $request;
    }

//    protected function getPaginator(): PaginatorInterface
//    {
//        return $this->container->get('knp_paginator');
//    }

	protected function getUser(): ?UserInterface
	{
		$user = parent::getUser();

		return $user instanceof UserInterface ? $user : null;
	}

	/** @param array<string, string> $params */
	protected function refreshOrRedirect(string $route, array $params = []): RedirectResponse
	{
		$referer = $this->requestStack->getMainRequest()?->headers->get('referer');

		return $this->redirect($referer ?? $this->generateUrl($route, $params));
	}

    /**
     * Gets the connected user e-mail.
     *
     * @return string|null the user e-mail or null if not connected
     */
    public function getUserEmail(): ?string
    {
        /** @psalm-var UserInterface|null $user */
        $user = $this->getUser();

        return $user?->getEmail();
    }

    /**
     * Gets the connected user identifier.
     *
     * @return string|null the user identifier or null if not connected
     */
    public function getUserIdentifier(): ?string
    {
        return $this->getUser()?->getUserIdentifier();
    }

	protected function redirectSeeOther(string $route, array $params = []): RedirectResponse
	{
		return $this->redirectToRoute($route, $params, Response::HTTP_SEE_OTHER);
	}

	protected function createUnprocessableEntityResponse(): Response
	{
		return new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
	}

    /**
     * Display a message, if not empty; and redirect to the home page.
     *
     * @param string    $message    the translatable message
     * @param array     $parameters the message parameters
     * @param FlashType $type       the message type
     * @param ?string   $domain     the translation domain
     * @param ?string   $locale     the translation locale
     *
     * @return RedirectResponse the response
     */
    public function redirectToHomePage(string $message = '', array $parameters = [], FlashType $type = FlashType::SUCCESS, string $domain = null, string $locale = null): RedirectResponse
    {
        if ('' !== $message) {
            $message = $this->trans($message, $parameters, $domain, $locale);
            $this->addFlashMessage($type, $message);
        }

        return $this->redirectToRoute(self::HOME_PAGE);
    }

    /**
     * Creates and returns a form helper instance.
     *
     * @param ?string    $labelPrefix the label prefix. If the prefix is not null, the label is automatically added when the field property is set.
     * @param mixed|null $data        the initial data
     * @param array      $options     the initial options
     */
    protected function createFormHelper(string $labelPrefix = null, mixed $data = null, array $options = []): FormHelper
    {
        $builder = $this->createFormBuilder($data, $options);

        return new FormHelper($builder, $labelPrefix);
    }

    /**
     * Gets the cookie path.
     */
    protected function getCookiePath(): string
    {
        return $this->getParameterString('cookie_path');
    }

    /**
     * Gets a string container parameter by its name.
     */
    protected function getParameterString(string $name): string
    {
        /** @psalm-var string $value */
        $value = $this->getParameter($name);

        return $value;
    }

    /**
     * Inspects the given request and calls submit() if the form was submitted, checks whether the given
     * form is submitted and if the form and all children are valid.
     *
     * @template T
     *
     * @psalm-param FormInterface<T> $form    the form to validate
     *
     * @see FormInterface::handleRequest()
     * @see FormInterface::isSubmitted()
     * @see FormInterface::isValid()
     */
    protected function handleRequestForm(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        return $form->isSubmitted() && $form->isValid();
    }

    /**
     * Returns the given exception as a JsonResponse.
     *
     * @param \Exception $e       the exception to serialize
     * @param ?string    $message the optional error message
     */
    protected function jsonException(\Exception $e, string $message = null): JsonResponse
    {
        return $this->jsonFalse([
            'message' => $message ?? $e->getMessage(),
            'exception' => $this->getExceptionContext($e),
        ]);
    }

    /**
     * Returns a Json response with false as result.
     *
     * @param array $data the data to merge within the response
     */
    protected function jsonFalse(array $data = []): JsonResponse
    {
        return $this->json(\array_merge_recursive(['result' => true, 'status' => 'error'], $data));
//        return $this->json(\array_merge_recursive(['result' => false, 'success' => false, 'status' => 'error'], $data));
    }

    /**
     * Returns a Json response with true as result.
     *
     * @param array $data the data to merge within the response
     */
    protected function jsonTrue(array $data = []): JsonResponse
    {
        return $this->json(\array_merge_recursive(['result' => true, 'status' => 'ok'], $data));
    }

    /**
     * Render the template exception.
     */
    protected function renderFormException(string $id, \Throwable $e, LoggerInterface $logger = null): Response
    {
        $message = $this->trans($id);
        $context = $this->getExceptionContext($e);
        $logger?->error($message, $context);

        return $this->render('@Twig/Exception/exception.html.twig', [
            'message' => $message,
            'exception' => $e,
        ]);
    }
}
