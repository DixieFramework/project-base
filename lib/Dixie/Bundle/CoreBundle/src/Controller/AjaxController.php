<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\CoreBundle\Exception\AjaxFormExceptionManager;
use Talav\CoreBundle\Traits\ContainerAwareTrait;
use Talav\CoreBundle\Traits\KernelAwareTrait;

class AjaxController extends AbstractController
{
    use KernelAwareTrait;

    /**
     * Executes an action requested via ajax.
     *
     * @return JsonResponse
     */
    #[Route('/c/ajax', name: 'core_ajax_delegate', methods: ['GET', 'POST'])]
    public function delegateAjaxAction(): Response
    {
        $request               = $this->getRequestStack()->getCurrentRequest();
        //process ajax actions
        $authenticationChecker = $this->kernel->getContainer()->get('security.authorization_checker');
        $location              = $this->getRequestString($request, 'location');
        $action                = $this->getRequestString($request, 'action');
        $bundleName            = null;
        if (empty($action)) {
            //check POST
            $action = $request->request->get('action');
        }


        if ($authenticationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if (strpos($action, ':') !== false) {
                //call the specified bundle's ajax action
                $parts     = explode(':', $action);
                $namespace = 'Talav';
                $isPlugin  = false;

                if (count($parts) == 3 && $parts['0'] == 'plugin') {
                    $namespace = 'TalavPlugin';
                    array_shift($parts);
                    $isPlugin = true;
                }

                if (count($parts) == 2) {
                    $bundleName = $parts[0];
                    $bundle     = ucfirst($bundleName);
                    $action     = $parts[1];
                    if (!$classExists = class_exists($namespace.'\\'.$bundle.'Bundle\\Controller\\AjaxController')) {
                        // Check if a plugin is prefixed with Mautic
                        $bundle      = 'Talav'.$bundle;
                        $classExists = class_exists($namespace.'\\'.$bundle.'Bundle\\Controller\\AjaxController');
                    } elseif (!$isPlugin) {
                        $bundle = 'Talav'.$bundle;
                    }

                    if ($classExists) {
                        return $this->forward(
                            "{$bundle}Bundle:Ajax:executeAjax",
                            [
                                'action' => $action,
                                //forward the request as well as Symfony creates a subrequest without GET/POST
                                'request' => $request,
                                'bundle'  => $bundleName,
                            ]
                        );
                    }
                } elseif (count($parts) == 3) {
                    $bundleName = $parts[0];
                    $bundle     = ucfirst($bundleName);
                    $controller = ucfirst($parts[1]);
                    $action     = $parts[2];
                    if (!$classExists = class_exists($className = $namespace.'\\'.$bundle.'Bundle\\Controller\\'.$controller.'Controller')) {
                        // Check if a plugin is prefixed with Mautic
                        $bundle      = 'Talav'.$bundle;
                        $classExists = class_exists($className = $namespace.'\\'.$bundle.'Bundle\\Controller\\'.$controller.'Controller');
                    } elseif (!$isPlugin) {
                        $bundle = 'Talav'.$bundle;
                    }

                    if ($classExists) {
                        return $this->forward(
                            $className . '::' . $action,
                            [
//                                'action' => $action,
//                                //forward the request as well as Symfony creates a subrequest without GET/POST
//                                'request' => $request,
//                                'bundle'  => $bundleName,
                            ]
                        );
                    }
                }
            }

            return $this->executeAjaxAction($action, $request, $bundleName);
        }

        return $this->json(['success' => 0]);
    }

    /**
     * @param         $action
     * @param Request $request
     * @param null    $bundle
     *
     * @return JsonResponse
     */
    public function executeAjaxAction($action, Request $request, $bundle = null)
    {
        if (method_exists($this, "{$action}Action")) {
            return $this->{"{$action}Action"}($request, $bundle);
        }

        return $this->json(['success' => 0]);
    }

	#[Route('/ajax/action', name: 'core_ajax_action', methods: ['GET', 'POST'])]
	public function ajaxAction(Request $request): Response
	{
		$this->request = $request;

		$location = $request->request->get('location');

		if (empty($location)) {
			return new JsonResponse(['status' => 5, 'message' => 'Unknown Location']);
		}

		$response = $this->forward('Talav\\' . str_replace('/', '\\', $location));
		if ($response->getStatusCode() == 500) {
			//500 Error -- Validation
			$em = AjaxFormExceptionManager::getInstance();
			if ($em->hasErrors()) {
				dump($em->getErrors());exit();
				return new JsonResponse(['errors' => $em->getErrors(), 'status' => 2]);
			}
		}

		return $response;
	}

	/**
	 * Render Html
	 *
	 * @param $template
	 * @param array $data
	 *
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
	 *
	 * @author AC ashwclark@outlook.com
	 */
	public function renderHtml($template, array $data = [])
	{
		return new JsonResponse(["html" => $this->renderView($template, $data), "status" => 1]);
	}
}
