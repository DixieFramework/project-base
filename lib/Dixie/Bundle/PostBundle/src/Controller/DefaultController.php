<?php

declare(strict_types=1);

namespace Talav\PostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;

#[AsController]
#[Route('/post', name: 'talav_post_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class DefaultController extends AbstractController
{
    #[Route('/{page<\d+>?1}', name: 'app_home',  methods: ['GET'])]
    public function index($page): Response
    {
        dd($page);
//        $this->updateLastActivity();
//        $gender = ($this->getUser()) ? $this->user()->getProfile()->getGender() : null;
//
//        $paginator
//            ->setClass(Post::class)
//            ->setMethod('findRecommendations')
//            ->setOrder(['publishedAt' => 'DESC'])
//            ->setCriteria(['gender' => $gender, 'status' => true, 'featured' => true])
//            ->setLimit(10)
//            ->setPage($page)
//        ;
//
//        return $this->render('interface/post/recommendations.html.twig', [
//            'posts' => $paginator->getData(),
//            'paginator' => $paginator
//        ]);
    }
}
