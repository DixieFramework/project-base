<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\BaseController;
use Talav\CoreBundle\Form\User\UserLoginType;
use Talav\CoreBundle\Traits\EventDispatcherAwareTrait;
use Talav\CoreBundle\Traits\KernelAwareTrait;
use Talav\WebBundle\Event\BuildJsEvent;
use Talav\WebBundle\TalavWebEvents;

#[Route('/js')]
class JsController extends BaseController
{
    use KernelAwareTrait;
    use EventDispatcherAwareTrait;

    #[Route('/idx')]
    public function index()
    {
        // Don't store a visitor with this request
        defined('MAUTIC_NON_TRACKABLE_REQUEST') || define('MAUTIC_NON_TRACKABLE_REQUEST', 1);

        $dispatcher = $this->dispatcher;
        $debug      = $this->kernel->isDebug();
        $event      = new BuildJsEvent($this->getJsHeader(), $debug);

        if ($dispatcher->hasListeners(TalavWebEvents::BUILD_TALAV_JS)) {
            $dispatcher->dispatch($event, TalavWebEvents::BUILD_TALAV_JS);
        }

        return new Response($event->getJs(), 200, ['Content-Type' => 'application/javascript']);

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

    /**
     * Return the edit item dialog template.
     */
    #[Route(path: '/ajax/dialog', name: 'ajax_dialog', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function renderModal(): JsonResponse
    {
        $parameters = [
            'form' => $this->createForm(UserLoginType::class),
        ];

        //return $this->render('@TalavWeb/test/tags_edit.html.twig', ['form' => $this->createForm(UserLoginType::class)->createView()]);
        return $this->renderDialog('@TalavWeb/test/tags_edit.html.twig', $parameters);
    }

    /**
     * Return the edit item dialog template.
     */
    #[Route(path: '/ajax/dialog/item', name: 'ajax_dialog_item', methods: Request::METHOD_GET)]
    public function renderItemDialog(): JsonResponse
    {
        $parameters = [
            'form' => $this->createForm(UserLoginType::class),
        ];

        return $this->renderDialog('@TalavWeb/dialog/dialog_edit_item.html.twig', $parameters);
    }

    /**
     * Build a JS header for the Mautic embedded JS.
     *
     * @return string
     */
    protected function getJsHeader()
    {
        $year = date('Y');

        return <<<JS
/**
 * @package     MauticJS
 * @copyright   {$year} Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
JS;
    }

    private function renderDialog(string $view, array $parameters): JsonResponse
    {
        return $this->json($this->renderView($view, $parameters));
    }
}
