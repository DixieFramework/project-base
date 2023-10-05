<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\WebBundle\Response\AjaxResponse;

#[Route(path: '/web/javascript', name: 'talav_web_javascript_')]
class JavascriptController extends AbstractController
{

    protected $ajaxResponse;

    public function __construct()
    {
        $this->ajaxResponse = new AjaxResponse();
    }

    #[Route(path: '/', name: 'index')]
    public function indexAction(Request $request): Response
    {
        return $this->render('@TalavWeb/index/index.html.twig');
        return new Response('Hello world from talav_web');
    }

    #[Route(path: '/alert', name: 'alert')]
    public function alert()
    {
        $this->ajaxResponse->alert('Test message');
        return new JsonResponse($this->ajaxResponse->getJson());
    }

    #[Route(path: '/modal', name: 'modal')]
    public function modal()
    {
        $this->ajaxResponse->modal('modal-sort', 'Test message');
        return new JsonResponse($this->ajaxResponse->getJson());
    }

    /**
     * This method helps redirect after ajax Request
     *
     * @param String $url prepared URL to redirect
     *
     * @return Response
     */
    public function ajaxRedirect($url)
    {
        $this->ajaxResponse->redirect($url);

        return new JsonResponse($this->ajaxResponse);
    }

    /**
     * @return Response
     */
    public function response()
    {
        return new JsonResponse($this->ajaxResponse);
    }

    /**
     * @return AjaxResponse
     */
    public function getAjaxResponse()
    {
        return $this->ajaxResponse;
    }
}
