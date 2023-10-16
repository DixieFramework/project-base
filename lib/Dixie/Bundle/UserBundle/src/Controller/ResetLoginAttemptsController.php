<?php

declare(strict_types=1);

namespace Talav\UserBundle\Controller;

use Talav\Component\Resource\Exception\FlashMessageTrait;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Traits\CommandBusAwareDispatchTrait;
use Talav\UserBundle\Message\Command\ResetLoginAttemptsCommand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class ResetLoginAttemptsController.

 */
#[AsController]
#[Route('/login/unlock/{token}', name: 'auth_login_attempts_reset', methods: ['GET'])]
final class ResetLoginAttemptsController extends AbstractController
{
	use CommandBusAwareDispatchTrait;
	use FlashMessageTrait;

    public function __invoke(string $token): Response
    {
        try {
            $this->dispatchSync(new ResetLoginAttemptsCommand($token));
            $this->successTrans(
                id: 'authentication.flashes.login_attempts_reset_successfully',
                domain: 'authentication'
            );
        } catch (\Throwable $e) {
            $this->addSafeMessageExceptionFlash($e);
        }

        return $this->redirectSeeOther('talav_user_login');
    }
}
