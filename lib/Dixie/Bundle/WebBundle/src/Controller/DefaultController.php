<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;

#[Route(path: '/web', name: 'talav_web_')]
class DefaultController extends AbstractController
{

    #[Route(path: '/', name: 'index')]
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_web');
    }
}
