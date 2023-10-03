<?php

declare(strict_types=1);

namespace Talav\PostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;

class DefaultController extends Abstractontroller
{
    /**
     * @Route("/talav_post")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_post');
    }
}
