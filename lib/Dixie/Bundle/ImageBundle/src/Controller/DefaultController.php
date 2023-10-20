<?php

declare(strict_types=1);

namespace Talav\ImageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;

#[AsController]
#[Route(path: 'image', name: 'image_')]
class DefaultController extends AbstractController
{
    #[Route(path: '/talav_image')]
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_image');
    }
}
