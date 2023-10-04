<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Application\Authentication\Command\RequestLoginLinkCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\UserBundle\Form\RequestLoginLinkForm;

/**
 * Class LoginLinkController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
#[Route('/login/link', name: 'auth_login_link_')]
final class LoginLinkController extends AbstractController
{
    #[Route('/request', name: 'request', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        if ($this->getUser()) {
            $this->redirectToRoute('app_index');
        }

        $command = new RequestLoginLinkCommand();
        $form = $this->createForm(RequestLoginLinkForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addSuccessFlash(
                    id: 'authentication.flashes.login_link_requested_successfully',
                    domain: 'authentication'
                );

                return $this->redirectSeeOther('auth_login');
            } catch (AuthenticationException) {
                $this->addSomethingWentWrongFlash();
            } catch (\Throwable $e) {
                $this->addSafeMessageExceptionFlash($e);
            }

            $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render(
            view: '@app/domain/authentication/login_link.html.twig',
            parameters: [
                'form' => $form,
            ],
            response: $response ?? null
        );
    }

    #[Route('/check', name: 'check', methods: ['GET'])]
    public function check(): never
    {
        throw new \LogicException('This code should never be reached');
    }
}
