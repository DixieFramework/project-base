<?php

namespace Groshy\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Service\SymfonyInfoService;

class HomeController extends AbstractController
{
	#[Route(path: '/', name: AbstractController::HOME_PAGE)]
	public function homeAction(SymfonyInfoService $service): Response
    {
        return $this->render('home/home.html.twig', []);
    }
}
