<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Groshy\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\BaseController;
use Talav\CoreBundle\Traits\EntityManagerAwareTrait;

#[Route('/profile/js', name: 'profile_js_')]
class JsController extends BaseController
{
	use EntityManagerAwareTrait;

	#[Route('/like/add', name: 'add_like')]
	public function like(Request $request)
	{
		/** @var Post $post */
		$post = $this->entityManager->getRepository(Post::class)->find($request->get('id'));

		$location = $request->get('location');

		//$response = $this->forward('Talav\ProfileBundle\Controller\\' . str_replace('/', '\\', $location), [

		//]);

		$response = $this->forward('Talav\ProfileBundle\Controller\LikeController::togglePostLike', [
			'post' => $post,
//			'request' => $request
		]);

		$h = sprintf("%d", $post->getLikes()->count());

		return $this->js()->updateHtml('#postLikeCount'.$request->get('id'), $h);
	}




    #[Route('')]
    public function index()
    {
        return $this->render('js/index.html.twig');
    }

    // modal

    #[Route('/modal')]
    public function modal()
    {
        return $this->js()->modal('@TalavCore/Modal/default.html.twig', [
            'title' => 'A modal',
            'content' => '...'
        ]);
    }

    #[Route('/modal/small')]
    public function smallModal()
    {
        return $this->js()->modal('@TalavCore/Modal/default.html.twig', [
            'title' => 'A modal',
            'content' => '...',
            'class' => 'modal-sm'
        ]);
    }

    #[Route('/modal/form')]
    public function formModal(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('Recipient', TextType::class, ['required' => false])
            ->add('message', TextareaType::class, ['required' => false])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->js()->closeModal();
        }

        return $this->js()->modal('@TalavCore/Modal/form.html.twig', [
            'title' => 'New message',
            'class' => 'modal-sm',
            'form' => $form->createView()
        ]);
    }

    #[Route('/modal/sleep')]
    public function sleepModal()
    {
        \sleep(1);

        return $this->js()->modal('@TalavCore/Modal/default.html.twig', [
            'title' => 'A modal',
            'content' => '...'
        ]);
    }

    // offcanvas

    #[Route('/offcanvas')]
    public function offcanvas()
    {
        return $this->js()->offcanvas('@TalavCore/Offcanvas/default.html.twig', [
            'title' => 'Left offcanvas',
            'content' => '...'
        ]);
    }

    #[Route('/offcanvas/left')]
    public function leftOffcanvas()
    {
        return $this->js()->offcanvas('@TalavCore/Offcanvas/default.html.twig', [
            'title' => 'Left offcanvas',
            'class' => 'offcanvas-start',
            'content' => '...'
        ]);
    }

    #[Route('/offcanvas/form')]
    public function formOffcanvas(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('Recipient', TextType::class, ['required' => false])
            ->add('message', TextareaType::class, ['required' => false])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->js()->closeOffcanvas();
        }

        return $this->js()->offcanvas('@TalavCore/Offcanvas/form.html.twig', [
            'title' => 'New message',
            'form' => $form->createView()
        ]);
    }

    // toast

    #[Route('/toast/success')]
    public function successToast()
    {
        return $this->js()->toastSuccess('...', 'Success toast');
    }

    #[Route('/toast/error')]
    public function errorToast()
    {
        return $this->js()->toastError('...', 'Error toast');
    }

    // dom

    #[Route('/dom/update')]
    public function updateDOM()
    {
        $now = new \DateTime();
        $h = sprintf('<div class="alert alert-info my-2">Server time %s</div>', $now->format(\DateTimeInterface::ISO8601));

        return $this->js()->updateHtml('#alert-stack', $h);
    }

    #[Route('/dom/remove')]
    public function removeDOM()
    {
        return $this->js()->remove('#alert-stack > *');
    }

    // web components

    #[Route('/menu/toggle')]
    public function toggleMenu()
    {
        return $this->js()->callWebComponent('[is=umbrella-sidebar]', 'toggle');
    }

    // custom

    #[Route('/custom')]
    public function custom()
    {
        return $this->js()->add('alert', ['text' => '...']);
    }
}
