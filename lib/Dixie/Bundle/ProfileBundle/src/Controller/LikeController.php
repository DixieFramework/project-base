<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CommentBundle\Entity\Comment;
use Talav\PostBundle\Entity\Post;
use Talav\PostBundle\Entity\PostInterface;

#[AsController]
#[Route(path: '/like', name: 'profile_like_')]
//#[IsGranted(RoleInterface::ROLE_USER)]
class LikeController extends AbstractController
{
	public function __construct(private readonly ManagerInterface $likeManager, private readonly ManagerInterface $notificationManager)
	{
	}

//	#[Route('/', name: 'index', methods: ['GET'])]
//    public function indexAction(Request $request): Response
//    {
//        return new Response('Hello world from talav_profile');
//    }

	#[Route('/post/{id}/toggle', name: 'toggle_post_like', methods: ['GET'])]
	public function togglePostLike(\Groshy\Entity\Post $post): Response
	{
		$likeRepo = $this->likeManager->getRepository();
		$notificationRepo = $this->notificationManager->getRepository();

		$like = $likeRepo->findOneBy(['user' => $this->getUser(), 'post' => $post]);

		if ($like) {
			$notification = $notificationRepo->findOneBy(['post' => $post, 'type' => 'post_like', 'sender' => $this->getUser()]);
			if ($notification) {
				$this->notificationManager->remove($notification);
			}

			$this->getUser()->removeLike($like);
			$response = ['status' => 'removed'];
		} else {
			$like = $this->likeManager->create();
			$like->setProfile($this->getUser());
			$like->setPost($post);
			$this->likeManager->update($like, true);
			$response = ['status' => 'added'];

			if ($post->getAuthor() !== $this->getUser()) {
				$existNotify = $notificationRepo->findOneBy(['post' => $post, 'type' => 'post_like']);

				if ($existNotify) {
					if ($existNotify->getSeen()) {
						$existNotify->setSeen(false);
						$existNotify->setQuantity(1);
					} elseif ($existNotify->getSender() !== $this->getUser() || $existNotify->getQuantity() > 1) {
						$existNotify->setQuantity($existNotify->getQuantity() + 1);
					}
					$existNotify->setPublishedAt(new \DateTime('now'));
					$existNotify->setSender($this->getUser());
				} else {
					$notification = $this->notificationManager->create();
					$notification->setType('post_like');
					$notification->setReceiver($post->getAuthor());
					$notification->setPost($post);
					$notification->setQuantity(1);
					$notification->setSender($this->getUser());
					$this->notificationManager->update($notification, true);
				}
			}
		}

		$this->entityManager->flush();

		return $this->json([
			'response' => $response
		]);
	}

	#[Route('/comment/{id}/toggle', name: 'toggle_comment_like', methods: ['GET'])]
    #[ParamConverter('comment', class: \Groshy\Entity\Comment::class, options: ['mapping' => ['id' => 'id']])]
	public function toggleCommentLike(\Groshy\Entity\Comment $comment, ManagerInterface $likeManager, ManagerInterface $notificationManager): Response
	{
		$likeRepo = $likeManager->getRepository();
		$notificationRepo = $notificationManager->getRepository();

		$like = $likeRepo->findOneBy(['user' => $this->getUser(), 'comment' => $comment]);

		if ($like) {
			$notification = $notificationRepo->findOneBy(['comment' => $comment, 'type' => 'comment_like', 'sender' => $this->getUser()]);
			if ($notification) {
				$notificationRepo->remove($notification);
			}

			$this->getUser()->removeLike($like);
			$response = ['status' => 'removed'];
		} else {
			$like = $likeManager->create();
			$like->setProfile($this->getUser());
			$like->setComment($comment);
			$likeManager->update($like, true);
			$response = ['status' => 'added'];

			if ($comment->getAuthor() !== $this->getUser()) {
				$existNotify = $notificationRepo->findOneBy(['comment' => $comment, 'type' => 'comment_like']);

				if ($existNotify) {
					if ($existNotify->getSeen()) {
						$existNotify->setSeen(false);
						$existNotify->setQuantity(1);
					} elseif ($existNotify->getSender() !== $this->getUser() || $existNotify->getQuantity() > 1) {
						$existNotify->setQuantity($existNotify->getQuantity() + 1);
					}
					$existNotify->setPublishedAt(new \DateTime('now'));
					$existNotify->setSender($this->getUser());
				} else {
					$notification = $notificationManager->create();
					$notification->setType('comment_like');
					$notification->setReceiver($comment->getAuthor());
					$notification->setComment($comment);
					if ($comment->getPost()) {
						$notification->setPost($comment->getPost());
					} else {
						$notification->setSong($comment->getSong());
					}
					$notification->setQuantity(1);
					$notification->setSender($this->getUser());
					$notificationManager->update($notification, true);
				}
			}
		}

		$this->entityManager->flush();

		return $this->json([
			'response' => $response
		]);
	}
}
