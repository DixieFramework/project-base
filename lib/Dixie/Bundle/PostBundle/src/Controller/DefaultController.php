<?php

declare(strict_types=1);

namespace Talav\PostBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\PostBundle\Form\Type\PostType;

#[AsController]
#[Route('/post', name: 'talav_post_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class DefaultController extends AbstractController
{
    public function __construct(private readonly ManagerInterface $postManager)
    {

    }

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

    #[Route('/new', name: 'post_new',  methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        $post = $this->postManager->create();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($user);
            $post->generateSlug($user);
            $this->postManager->update($post, true);

            if (!$defender->rightToSetTitleSlug($post)) {
                $form->get('title')->addError(new FormError($this->trans('title.or.slug.exists')));
            } elseif (!$post->getImage()) {
                $form->get('imageFile')->addError(new FormError($this->trans('post.image.required')));
            } else {
                return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
            }
        }

        return $this->render('@TalavPost/post/new.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
}
