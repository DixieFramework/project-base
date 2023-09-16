<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CoreBundle\Model\PasswordQuery;
use Talav\CoreBundle\Service\PasswordService;
use Talav\CoreBundle\Traits\CookieTrait;
use Talav\CoreBundle\Traits\MathTrait;

/**
 * Controller for all XMLHttpRequest (Ajax) calls.
 */
#[AsController]
#[Route(path: '/ajax')]
class AjaxController extends AbstractController
{
    use CookieTrait;
    use MathTrait;

    /**
     * Validate a strength password.
     */
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/password', name: 'ajax_password', methods: Request::METHOD_POST)]
    public function password(#[MapRequestPayload] PasswordQuery $query, PasswordService $service): JsonResponse
    {
        $results = $service->validate($query);

        return $this->json($results);
    }

    private function renderDialog(string $view, array $parameters = []): JsonResponse
    {
        return $this->json($this->renderView($view, $parameters));
    }
}
