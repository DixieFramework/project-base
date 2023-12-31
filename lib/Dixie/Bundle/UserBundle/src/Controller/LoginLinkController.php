<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Talav\CoreBundle\Traits\CommandBusAwareDispatchTrait;
use Talav\UserBundle\Message\Command\RequestLoginLinkCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\UserBundle\Form\RequestLoginLinkForm;

#[AsController]
#[Route('/login/link', name: 'auth_login_link_')]
final class LoginLinkController extends AbstractController
{
	use CommandBusAwareDispatchTrait;

    #[Route('/request', name: 'request', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        if ($this->getUser()) {
            $this->redirectToRoute(AbstractController::HOME_PAGE);
        }

        $command = new RequestLoginLinkCommand();
        $form = $this->createForm(RequestLoginLinkForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->successTrans(
                    id: 'authentication.flashes.login_link_requested_successfully',
                    domain: 'authentication'
                );

                return $this->redirectSeeOther('talav_user_login');
            } catch (AuthenticationException) {
                $this->addSomethingWentWrongFlash();
            } catch (\Throwable $e) {
				dd($e);
                $this->addSafeMessageExceptionFlash($e);
            }

            $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render(
            view: '@TalavUser/login_link/login_link.html.twig',
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
