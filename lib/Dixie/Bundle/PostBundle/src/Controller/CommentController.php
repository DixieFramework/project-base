<?php

declare(strict_types=1);

namespace Talav\PostBundle\Controller;

use Groshy\Entity\Post;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\PostBundle\Entity\PostInterface;
use Talav\PostBundle\Form\Type\CommentType;
use Talav\PostBundle\Form\Type\PostType;
use Talav\PostBundle\Repository\CommentRepository;

#[AsController]
#[Route('/post', name: 'talav_post_comment_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class CommentController extends AbstractController
{
    /**
     * @var int
     */
    final public const COMMENTS_PER_PAGE = 10;

    public function __construct(private readonly ManagerInterface $postCommentManager)
    {
    }

    #[Route(path: '/form/{id<%patterns.id%>}', name: 'app_comment_form', methods: ['GET'])]
    public function form(Post $event, int $page = 1): Response
    {
        $comment = $this->postCommentManager->create();
        $form = null;
        if ($this->isGranted('ROLE_USER')) {
            $form = $this
                ->createForm(CommentType::class, $comment, [
                    'action' => $this->generateUrl('talav_post_comment_app_comment_new', ['id' => $event->getId()]),
                ])
                ->createView();
        }

        $comments = $this->createQueryBuilderPaginator(
            $this->postCommentManager->getRepository()->findAllByEventQueryBuilder($event),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavPost/comment/list-and-form.html.twig', [
            'comments' => $comments,
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route(path: '/comments/{id<%patterns.id%>}/{page<%patterns.page%>}', name: 'app_comment_list', methods: ['GET'])]
//    #[ReverseProxy(expires: 'tomorrow')]
    public function list(Post $event, int $page = 1): Response
    {
        $comments = $this->createQueryBuilderPaginator(
            $this->postCommentManager->getRepository()->findAllByEventQueryBuilder($event),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavPost/comment/list.html.twig', [
            'comments' => $comments,
            'event' => $event,
            'page' => $page,
            'offset' => self::COMMENTS_PER_PAGE,
        ]);
    }

    #[Route(path: '/{id<%patterns.id%>}/nouveau', name: 'app_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Post $event): Response
    {
        $user = $this->getUser();

        $comment = $this->postCommentManager->create();
        $comment->setAuthor($user);
        $comment->setPost($event);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('talav_post_comment_app_comment_new', ['id' => $event->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->postCommentManager->update($comment, true);

            $comments = $this->createQueryBuilderPaginator(
                $this->postCommentManager->getRepository()->findAllByEventQueryBuilder($event),
                1,
                self::COMMENTS_PER_PAGE
            );

            return new JsonResponse([
                'success' => true,
                'comment' => $this->renderView('@TalavPost/comment/_details.html.twig', [
                    'comment' => $comment,
                    'success' => true,
                ]),
                'header' => $this->renderView('@TalavPost/comment/_header.html.twig', [
                    'comments' => $comments,
                ]),
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'post' => $this->renderView('@TalavPost/comment/form.html.twig', [
                'form' => $form->createView(),
            ]),
        ]);
    }
}
