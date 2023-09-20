<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Controller;

use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\SettingsBundle\Exception\InvalidScopeException;
use Talav\SettingsBundle\Form\Type\BulkSettingsUpdateType;
use Talav\SettingsBundle\Form\Type\SettingType;
use Talav\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingsController extends AbstractController
{
    protected $settingsManager;

    protected $scopeContext;

    protected $formFactory;

    public function __construct(
        SettingsManagerInterface $settingsManager,
        ScopeContextInterface $scopeContext,
        FormFactoryInterface $formFactory
    ) {
        $this->settingsManager = $settingsManager;
        $this->scopeContext = $scopeContext;
        $this->formFactory = $formFactory;
    }

    public function list(): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->settingsManager->all());
    }

    public function revert(string $scope): SingleResourceResponseInterface
    {
        $this->settingsManager->clearAllByScope($scope);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    public function update(Request $request): SingleResourceResponseInterface
    {
        $form = $this->formFactory->createNamed('', SettingType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $setting = $this->settingsManager->getOneSettingByName($data['name']);

            if (null === $setting) {
                throw new NotFoundHttpException('Setting with this name was not found.');
            }

            $scope = $setting['scope'];
            $owner = null;
            if (ScopeContextInterface::SCOPE_GLOBAL !== $scope) {
                $owner = $this->scopeContext->getScopeOwner($scope);
                if (null === $owner) {
                    throw new InvalidScopeException($scope);
                }
            }

            $setting = $this->settingsManager->set($data['name'], $data['value'], $scope, $owner);

            return new SingleResourceResponse($setting);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    public function bulk(Request $request): SingleResourceResponseInterface
    {
        $form = $this->formFactory->createNamed('', BulkSettingsUpdateType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ((array) $data['bulk'] as $item) {
                $setting = $this->settingsManager->getOneSettingByName($item['name']);
                if (null === $setting) {
                    throw new NotFoundHttpException(sprintf('Setting with "%s" name was not found.', $item['name']));
                }

                $scope = $setting['scope'];
                $owner = null;
                if (ScopeContextInterface::SCOPE_GLOBAL !== $scope) {
                    $owner = $this->scopeContext->getScopeOwner($scope);
                    if (null === $owner) {
                        throw new InvalidScopeException($scope);
                    }
                }

                $this->settingsManager->set($item['name'], $item['value'], $scope, $owner);
            }

            return new SingleResourceResponse($this->settingsManager->all());
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
