<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        $action                = $this->getRequestString($request, 'action');
        $bundleName            = null;
        if (empty($action)) {
            //check POST
            $action = $request->request->get('action');
        }
dd($authenticationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'));
        if ($authenticationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if (strpos($action, ':') !== false) {
                //call the specified bundle's ajax action
                $parts     = explode(':', $action);
                $namespace = 'Mautic';
                $isPlugin  = false;

                if (count($parts) == 3 && $parts['0'] == 'plugin') {
                    $namespace = 'MauticPlugin';
                    array_shift($parts);
                    $isPlugin = true;
                }

                if (count($parts) == 2) {
                    $bundleName = $parts[0];
                    $bundle     = ucfirst($bundleName);
                    $action     = $parts[1];
                    if (!$classExists = class_exists($namespace.'\\'.$bundle.'Bundle\\Controller\\AjaxController')) {
                        // Check if a plugin is prefixed with Mautic
                        $bundle      = 'Mautic'.$bundle;
                        $classExists = class_exists($namespace.'\\'.$bundle.'Bundle\\Controller\\AjaxController');
                    } elseif (!$isPlugin) {
                        $bundle = 'Mautic'.$bundle;
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
}
