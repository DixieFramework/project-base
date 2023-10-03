<?php

declare(strict_types=1);

namespace Talav\PostBundle\Controller;

use Groshy\Entity\Post;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\PostBundle\Entity\PostInterface;
use Talav\PostBundle\Form\Type\PostType;

#[AsController]
#[Route('/post', name: 'talav_post_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class DefaultController extends AbstractController
{
    /**
     * @var int
     */
    final public const COMMENTS_PER_PAGE = 10;

    public function __construct(private readonly ManagerInterface $postManager)
    {

    }

    #[Route('/{page<\d+>?1}', name: 'app_home',  methods: ['GET'])]
    public function index(int $page): Response
    {
        $posts = $this->createQueryBuilderPaginator(
            $this->postManager->getRepository()->findRecommendations(['status' => true, 'featured' => true]),
            $page,
            self::COMMENTS_PER_PAGE
        );

        return $this->render('@TalavPost/post/recommendations.html.twig', [
            'posts' => $posts
        ]);

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

	#[Route('/view/{id}/{page<\d+>?1}', name: 'post_show',  methods: ['GET'])]
	public function show(Post $post, $page): Response
	{
		$followed = true;//$followRepo->findOneBy(['follower' => $this->getUser(), 'followed' => $post->getAuthor(), 'accepted' => true]);
		if ($followed && $post->getStatus() === true || $post->getStatus() === true && $post->getAuthor()->getClosedAccount() === false || $this->getUser() === $post->getAuthor() || $this->isGranted('ROLE_OWNER')) {
			return $this->render('@TalavPost/post/show.html.twig', [
				'post' => $post,
				'page' => $page
			]);
		} else {
			return $this->redirectToRoute('user_profile_view', [
				'username' => $post->getAuthor()->getUsername()
			]);
		}
	}

	#[Route('/post/{id}/edit', name: 'post_edit',  methods: ['GET','POST'])]
	#[Security('is_granted("ROLE_USER")')]
	public function edit(Request $request, Post $post, Initializer $initializer, Defender $defender): Response
	{
		if ($this->user() !== $post->getAuthor() && !$this->isGranted('ROLE_POST_MODERATOR')) {
			return $this->redirectToRoute('app_home');
		}

		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$initializer->initializePostEdit($post, $form);

			if (!$defender->rightToSetTitleSlug($post)) {
				$form->get('title')->addError(new FormError($this->trans('title.or.slug.exists')));
			} else {
				return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
			}
		}

		return $this->render('interface/post/edit.html.twig', [
			'post' => $post,
			'form' => $form->createView(),
		]);
	}

	#[Route('/post/{id}/delete', name: 'post_delete',  methods: ['POST'])]
	#[Security('is_granted("ROLE_USER")')]
	public function delete(Request $request, Post $post, PostRepository $postRepo): Response
	{
		if ($this->user() !== $post->getAuthor() && !$this->isGranted('ROLE_OWNER')) {
			return $this->redirectToRoute('app_home');
		}

		$username = $post->getAuthor()->getUsername();

		if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
			$postRepo->remove($post);
		}

		return $this->redirectToRoute('user_profile', [
			'username' => $username
		]);
	}
}
