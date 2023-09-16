<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CoreBundle\Traits\CookieTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller to handle the license agreement.
 */
#[AsController]
#[Route(path: '/policy')]
#[IsGranted(RoleInterface::ROLE_USER)]
class PolicyController extends AbstractController
{
    use CookieTrait;

    final public const POLICY_ACCEPTED = 'POLICY_ACCEPTED';

    /**
     * Accept the license agreement.
     */
    #[Route(path: '/accept', name: 'policy_accept')]
    public function invoke(): RedirectResponse
    {
        $path = $this->getCookiePath();
        $response = $this->redirectToHomePage('cookie_banner.success');
        $this->updateCookie($response, self::POLICY_ACCEPTED, true, path: $path);

        return $response;
    }
}
