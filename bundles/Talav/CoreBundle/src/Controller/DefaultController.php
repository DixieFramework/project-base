<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;

class DefaultController extends Abstractontroller
{
    /**
     * @Route("/talav_core")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_core');
    }
}
