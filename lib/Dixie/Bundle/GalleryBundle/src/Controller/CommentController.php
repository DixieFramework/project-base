<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use Talav\CoreBundle\Controller\AbstractController;
use Talav\GalleryBundle\Entity\Comment;
use Talav\GalleryBundle\Entity\Image;
use Talav\GalleryBundle\Form\Type\CommentType;
use Talav\GalleryBundle\Repository\CommentRepository;
use Talav\GalleryBundle\Voter\CommentVoter;
use Talav\GalleryBundle\Service\CommentServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[Route('/comments')]
class CommentController extends AbstractController
{
    /**
     * @var int
     */
    final public const COMMENTS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CommentRepository $commentRepository Comment service
     * @param TranslatorInterface     $translator     Translator
     */
    //public function __construct(private CommentServiceInterface $commentService, private TranslatorInterface $translator)
    public function __construct(private CommentRepository $commentRepository, private TranslatorInterface $translator)
    {
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Image   $image   Image entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create/{id}',
        name: 'comment_create',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, Image $image): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::CREATE);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setImage($image);
            $this->commentRepository->save($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_preview', ['id' => $image->getGallery()->getId()]);
        }

        return $this->render(
            'comments/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * List action.
     *
     * @param Request $request HTTP request
     * @param Image   $image   Image entity
     *
     * @return Response HTTP response
     */
//    #[Route(
//        '/list/{id}',
//        name: 'comment_list',
//        requirements: ['id' => '[1-9]\d*'],
//        methods: 'GET',
//    )]
    #[Route(path: '/{id<%patterns.id%>}/{page<%patterns.page%>}', name: 'app_comment_list', methods: ['GET'])]
    public function list(Request $request, Image $image, int $page = 1): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::LIST);

        $comments = $this->createQueryBuilderPaginator(
            $this->commentRepository->findAllByImageQueryBuilder($image),
            $page,
            self::COMMENTS_PER_PAGE
        );

        //$page = $request->query->getInt('page', 1);

        return $this->render(
            'comments/list.html.twig',
            [
                'image' => $image,
                'comments' => $this->commentService->getPaginatedList($image, $page),
            ],
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/delete/{id}',
        name: 'comment_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE',
    )]
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'comments/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
