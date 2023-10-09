<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CommentBundle\Form\Type\CommentType;
use Talav\GalleryBundle\Entity\GalleryImage;
use Talav\PostBundle\Entity\PostInterface;
use Talav\WebBundle\Service\PaginatorService;

#[AsController]
#[Route('/comment', name: 'talav_comment_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class CommentController extends AbstractController
{
    /**
     * @var int
     */
    final public const COMMENTS_PER_PAGE = 10;

    public function __construct(private readonly ManagerInterface $commentManager, private readonly PaginatorService $paginator)
    {
    }

    #[Route(path: '/form/{type}/{entityId<%patterns.id%>}', name: 'app_comment_form', methods: ['GET'])]
    public function form($type, int $entityId, int $page = 1): Response
    {
		switch ($type) {
			case 'post':
				$entity = $this->entityManager->getRepository(PostInterface::class)->find($entityId);
				break;
			case 'gallery-image':
				$entity = $this->entityManager->getRepository(GalleryImage::class)->find($entityId);
				break;
			default:
				throw $this->createNotFoundException();
				break;
		}

        $comment = $this->commentManager->create();
        $form = null;
        if ($this->isGranted('ROLE_USER')) {
            $form = $this
                ->createForm(CommentType::class, $comment, [
                    'action' => $this->generateUrl('talav_comment_app_comment_new', ['id' => $entityId]),
                ])
                ->createView();
        }

        $comments = $this->createQueryBuilderPaginator(
            $this->commentManager->getRepository()->findCommentsByTypeAndEntityQueryBuilder($type, $entityId),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavComment/comment/list-and-form.html.twig', [
            'comments' => $comments,
            'entity' => $entity,
            'form' => $form,
        ]);
    }

    #[Route(path: '/form/{id<%patterns.id%>}', name: 'app_post_comment_form', methods: ['GET'])]
    public function postForm(Post $event, int $page = 1): Response
    {
        $comment = $this->commentManager->create();
        $form = null;
        if ($this->isGranted('ROLE_USER')) {
            $form = $this
                ->createForm(CommentType::class, $comment, [
                    'action' => $this->generateUrl('talav_post_comment_app_comment_new', ['id' => $event->getId()]),
                ])
                ->createView();
        }

        $comments = $this->createQueryBuilderPaginator(
            $this->commentManager->getRepository()->findAllByPostQueryBuilder($event),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavPost/comment/list-and-form.html.twig', [
            'comments' => $comments,
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route(path: '/comments/{type}/{entityId<%patterns.id%>}/{page<%patterns.page%>}', name: 'app_comment_list', methods: ['GET'])]
//    #[ReverseProxy(expires: 'tomorrow')]
    public function list($type, int $entityId, int $page = 1): Response
    {
//        $name = strtolower((new \ReflectionClass($event))->getShortName());
//dd($name);
//        $this->paginator->setClass(Comment::class)
//            ->setMethod('getNoParentComments')
//            ->setType('comments')
//            ->setOrder(['id' => 'DESC'])
//            ->setCriteria([$name => $event])
//            ->setParameters(['id' => $event->getId()])
//            ->setLimit(30)
//            ->setPage($page);
//dd($this->paginator->getData());

		switch ($type) {
			case 'post':
				$entity = $this->entityManager->getRepository(PostInterface::class)->find($entityId);
				break;
			case 'gallery-image':
				$entity = $this->entityManager->getRepository(GalleryImage::class)->find($entityId);
				break;
			default:
				throw $this->createNotFoundException();
				break;
		}

        $name = strtolower((new \ReflectionClass($entity))->getShortName());

        $comments = $this->createQueryBuilderPaginator(
            $this->commentManager->getRepository()->findAllQueryBuilder(['type' => $name, 'entityId' => $entity]),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavComment/comment/list.html.twig', [
            'comments' => $comments,
            'entity' => $entity,
            'page' => $page,
            'offset' => self::COMMENTS_PER_PAGE,
        ]);
    }

    #[Route(path: '/{id<%patterns.id%>}/nouveau', name: 'app_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Post $event): Response
    {
        $user = $this->getUser();

        $comment = $this->commentManager->create();
        $comment->setAuthor($user);
        $comment->setPost($event);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('talav_post_comment_app_comment_new', ['id' => $event->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentManager->update($comment, true);

            $comments = $this->createQueryBuilderPaginator(
                $this->commentManager->getRepository()->findAllByPostQueryBuilder($event),
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
