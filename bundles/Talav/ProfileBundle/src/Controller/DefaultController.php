<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;

class DefaultController extends Abstractontroller
{
    /**
     * @Route("/talav_profile")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_profile');
    }
}
