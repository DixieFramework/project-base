<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Controller;

use Groshy\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CommentBundle\Entity\CommentInterface;
use Talav\CommentBundle\Form\Type\CommentType;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;

class ReplyController extends AbstractController
{
    /**
     * @var int
     */
    final public const REPLIES_PER_PAGE = 5;

    public function __construct(private readonly ManagerInterface $commentManager)
    {
    }

    #[Route(path: '/{id<%patterns.id%>}/reponses/{page<%patterns.page%>}', name: 'app_comment_reponse_list', methods: ['GET'])]
    public function list(Comment $comment, int $page = 1): Response
    {
        $comments = $this->createQueryBuilderPaginator(
            $this->commentManager->getRepository()->findAllAnswersQueryBuilder($comment),
            $page,
            self::REPLIES_PER_PAGE
        );

        return $this->render('@TalavComment/comment/reply/list.html.twig', [
            'comments' => $comments,
            'mainComment' => $comment,
            'page' => $page,
            'offset' => self::REPLIES_PER_PAGE,
        ]);
    }

    #[Route(path: '/{id<%patterns.id%>}/repondre', name: 'app_comment_reponse_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Comment $comment): Response
    {
        $user = $this->getUser();

        /** @var CommentInterface $reponse */
        $reponse = $this->commentManager->create();
        $reponse->setAuthor($user);
        $reponse->setType($comment->getType());
        $reponse->setEntityId($comment->getEntityId());

        $form = $this->createForm(CommentType::class, $reponse, [
            'action' => $this->generateUrl('app_comment_reponse_new', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $comment->addChildren($reponse);
            $em = $this->entityManager;
            $em->persist($comment);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'comment' => $this->renderView('@TalavComment/reply/details.html.twig', [
                    'comment' => $reponse,
                    'success' => true,
                ]),
            ]);
        } elseif ($form->isSubmitted()) {
            return new JsonResponse([
                'success' => false,
                'post' => $this->renderView('@TalavComment/reply/form.html.twig', [
                    'comment' => $comment,
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('@TalavComment/reply/form.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
}
