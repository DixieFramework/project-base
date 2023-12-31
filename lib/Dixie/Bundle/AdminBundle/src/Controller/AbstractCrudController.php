<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Talav\AdminBundle\Traits\DeleteCsrfTrait;
use Talav\Component\Resource\Exception\FlashMessageTrait;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Repository\DataRepositoryInterface;
use Talav\CoreBundle\Traits\CommandBusAwareDispatchTrait;
use Talav\CoreBundle\Traits\PaginateTrait;

abstract class AbstractCrudController extends AbstractController
{
    use CommandBusAwareDispatchTrait;
    use PaginateTrait;
    use FlashMessageTrait;

    use DeleteCsrfTrait;

    protected const ROUTE_PREFIX = 'admin_';
    protected const DOMAIN = 'shared';
    protected const ENTITY = 'default';

    public function handleCommand(object $command, CrudParams $params = new CrudParams()): Response
    {
        return match ($params->action) {
            CrudAction::DELETE => $this->handleDelete($command, $params),
            CrudAction::CREATE, CrudAction::UPDATE => $this->handleWithForm($command, $params),
            default => $this->handleDefault($command, $params)
        };
    }

    public function getViewPath(string $name): string
    {
        return sprintf('@TalavAdmin/%s/%s/%s.html.twig', static::DOMAIN, static::ENTITY, $name);
    }

    public function getFormViewPath(?string $name = null, bool $override = false): string
    {
        return match (true) {
            $override => $this->getViewPath((string) $name),
            default => '@admin/shared/layout/form.html.twig'
        };
    }

    public function getRouteName(string $name, bool $override = false): string
    {
        return match (true) {
            $override => $name,
            default => sprintf('%s%s_%s_%s', self::ROUTE_PREFIX, static::DOMAIN, static::ENTITY, $name)
        };
    }

//    public function queryIndex(DataRepositoryInterface $repository): Response
    public function queryIndex(RepositoryInterface $repository): Response
    {
//        dd($this->getArrayPaginator(                    objects: $repository->findBy([]),
//            page: $this->getCurrentRequest()->query->getInt('page', 1),
//            limit: 50));
        return $this->render(
            view: $this->getViewPath('index'),
            parameters: [
                'data' => $this->getArrayPaginator(
                    objects: $repository->findBy([]),
                    page: $this->getCurrentRequest()->query->getInt('page', 1),
                    limit: 50
                ),
//                'data' => $this->paginate(
//                    target: $repository->findBy([]),
//                    page: $this->getCurrentRequest()->query->getInt('page', 1),
//                    limit: 50
//                ),
            ]
        );
//        return $this->render(
//            view: $this->getViewPath('index'),
//            parameters: [
//                'data' => $this->getPaginator()->paginate(
//                    target: $repository->findBy([]),
//                    page: $this->getCurrentRequest()->query->getInt('page', 1),
//                    limit: 50
//                ),
//            ]
//        );
    }

    private function handleDefault(object $command, CrudParams $params = new CrudParams()): Response
    {
        try {
            $this->dispatchSync($command);
            $this->addSuccessfullActionFlash();
        } catch (\Throwable $e) {
            $this->addSafeMessageExceptionFlash($e);
        }

        if ($params->item) {
            return $this->redirectSeeOther(
                route: $this->getRouteName('show'),
                params: [
                    'id' => $params->item->getId(),
                ]
            );
        }

        return $this->redirectSeeOther($this->getRouteName('index'));
    }

    private function handleWithForm(object $command, CrudParams $params = new CrudParams()): Response
    {
        $request = $this->getCurrentRequest();
        $turbo = $request->headers->get('Turbo-Frame');
        $form = $this->createForm((string) $params->formClass, $command, [
            'action' => $this->generateUrl(
                route: $request->attributes->getString('_route'),
                parameters: (array) $request->attributes->get('_route_params', []),
            ),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addSuccessfullActionFlash();

                if ($params->item && $params->hasShow) {
                    return $this->redirectSeeOther(
                        route: $this->getRouteName('show'),
                        params: [
                            'id' => $params->item->getId(),
                        ]
                    );
                }

                return match (true) {
                    null !== $params->redirectUrl => new RedirectResponse($params->redirectUrl, Response::HTTP_SEE_OTHER),
                    default => $this->redirectSeeOther($this->getRouteName('index'))
                };
            } catch (\Throwable $e) {
                match (true) {
                    null !== $turbo => $form->addError($this->addSafeMessageExceptionError($e)),
                    default => $this->addSafeMessageExceptionFlash($e)
                };

                $response = $this->createUnprocessableEntityResponse();
            }
        }

        return $this->render(
            view: $this->getFormViewPath($params->view, $params->overrideView),
            parameters: [
                'form' => $form,
                'data' => $params->item,
                '_domain' => static::DOMAIN,
                '_entity' => static::ENTITY,
                '_turbo_frame_target' => $turbo,
                '_index_url' => false !== $params->hasIndex ? $this->generateUrl($this->getRouteName('index')) : null,
                '_show_url' => false !== $params->hasShow ? $this->generateUrl($this->getRouteName('show'), [
                    'id' => $params->item?->getId(),
                ]) : null,
            ],
            response: $response ?? null
        );
    }

    private function handleDelete(object $command, CrudParams $params = new CrudParams()): Response
    {
        $isXmlHttpRequest = $this->getCurrentRequest()->isXmlHttpRequest();

        if ($params->item && $this->isDeleteCsrfTokenValid($params->item)) {
            try {
                $this->dispatchSync($command);

                if ($isXmlHttpRequest) {
                    return new JsonResponse(null, Response::HTTP_ACCEPTED);
                }

                $this->addSuccessfullActionFlash('suppression');
            } catch (\Throwable $e) {
                if ($isXmlHttpRequest) {
                    return new JsonResponse([
                        'message' => $this->getSafeMessageException($e),
                    ], Response::HTTP_BAD_REQUEST);
                }

                $this->addSafeMessageExceptionFlash($e);
            }
        }

        return match (true) {
            null !== $params->redirectUrl => new RedirectResponse($params->redirectUrl, Response::HTTP_SEE_OTHER),
            default => $this->redirectSeeOther($this->getRouteName('index'))
        };
    }
}
