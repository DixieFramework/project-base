<?php

namespace Groshy\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Service\ContainerService\RouterAwareTrait;
use Talav\CoreBundle\Service\SymfonyInfoService;
use Talav\PermissionBundle\Security\PermissionLoaderInterface;

class HomeController extends AbstractController
{
	#[Route(path: '/', name: AbstractController::HOME_PAGE)]
	public function homeAction(SymfonyInfoService $service, PermissionLoaderInterface $permissionLoader, UrlGeneratorInterface $generator): Response
    {
		//$this->denyAccessUnlessGranted('edit_own_profile');
//		dd($permissionLoader);
//		dd($this->container->get('talav_permission.security.config_permission_loader'));
        return $this->render('home/home.html.twig', []);
    }
}
