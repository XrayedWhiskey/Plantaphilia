<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\WordPressAdminDriver;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\Dashboard\DashboardController;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Layout;
use Throwable;

/**
 * The **ControllerManager** class loads the requested backend modules and takes care of displaying the backend layout.
 *
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()
 */
final class ControllerManager
{
    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private Container $container;

    private object $displayStack;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Layout $layout;

    private ControllerInterface $loadedController;

    private MessageManager $messageManager;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        Container $container,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Layout $layout,
        MessageManager $messageManager,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->container = $container;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->layout = $layout;
        $this->messageManager = $messageManager;
        $this->wpFunction = $wpFunction;

        $this->displayStack = (object) [
            'header' => [],
            'content' => [],
            'footer' => [],
        ];
    }

    public function getLoadedController(): ControllerInterface
    {
        return $this->loadedController;
    }

    /**
     * This method handles the loading of the requested controller.
     *
     * When an exception is thrown, this method catches it and displays the message.
     *
     * The controller must be in the namespace of the backend!
     *
     * @see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::loadController()
     * @see \Borlabs\Cookie\System\WordPressAdminDriver\WordPressAdminInit::addMenu()
     *
     * @param string $controllerClass The controller class located in Controller/Backend, e.g.
     *                                Dashboard\DashboardController
     */
    public function load(string $controllerClass): void
    {
        /**
         * The RequestDto is created in {@see \Borlabs\Cookie\System\WordPressAdminDriver\WordPressAdminInit::register()}.
         *
         * @var RequestDto $request
         */
        $request = $this->container->get('currentRequest');
        $this->displayStack->header[] = $this->layout->getHeader();
        $this->displayStack->footer[] = $this->layout->getFooter();

        try {
            $this->loadController($controllerClass, $request);
        } catch (Throwable $throwable) {
            $this->messageManager->error(
                $throwable->getFile() . ':' . $throwable->getLine() . ': ' . esc_html($throwable->getMessage()),
            );
            $this->messageManager->error(nl2br(esc_html($throwable->getTraceAsString())));
            // Contains empty request data in case of an exception to avoid an exception loop
            $this->loadController($controllerClass, new RequestDto([], [], []));
        }

        $this->displayStack->header[] = $this->layout->getNavigation($this->getLoadedController(), $request);
        $this->output();
    }

    /**
     * Loads the requested controller and calls its route() method.
     */
    private function loadController(string $controllerClass, RequestDto $request): void
    {
        if (class_exists($controllerClass)) {
            $this->loadedController = $this->container->get($controllerClass);

            // If request is not valid, return to overview
            if ($this->routeValidation($this->loadedController, $request) === false) {
                $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['routeValidationFailed']);
                $this->displayStack->content[] = $this->loadedController->route(new RequestDto([], [], []));

                return;
            }

            $this->displayStack->content[] = call_user_func([$this->loadedController, 'route'], $request);

            return;
        }

        // Fallback to DashboardController
        $this->loadedController = $this->container->get(DashboardController::class);
        $this->displayStack->content[] = $this->loadedController->route($request);
    }

    private function output(): void
    {
        foreach ($this->displayStack->header as $content) {
            echo $content;
        }

        foreach ($this->displayStack->content as $content) {
            echo $content;
        }

        echo $this->borlabsCookieGlobalsService->getInlineJavaScript();

        foreach ($this->displayStack->footer as $content) {
            echo $content;
        }
    }

    private function routeValidation(ControllerInterface $controller, RequestDto $request): bool
    {
        $isValid = false;
        $nonce = $request->postData['_wpnonce'] ?? $request->getData['_wpnonce'] ?? '';
        $controllerId = $controller::CONTROLLER_ID;

        if (!isset($request->postData['action']) && !isset($request->getData['action'])) {
            $isValid = true;
        }

        if (
            isset($request->getData['action'], $request->getData['id'])
            && in_array($request->getData['action'], ['delete', 'details', 'edit', 'switch-status',], true)
            && $this->wpFunction->wpVerifyNonce($controllerId . '-' . $request->getData['id'] . '-' . $request->getData['action'], $nonce) === true
        ) {
            $isValid = true;
        }

        if (
            isset($request->getData['action'])
            && !isset($request->getData['id'])
            && in_array($request->getData['action'], ['search'], true)
            && $this->wpFunction->wpVerifyNonce($controllerId . '-' . $request->getData['action'], $nonce) === true
        ) {
            $isValid = true;
        }

        if (
            isset($request->postData['action'], $request->postData['id'])
            && in_array($request->postData['action'], ['delete', 'save',], true)
            && $this->wpFunction->wpVerifyNonce($controllerId . '-' . $request->postData['id'] . '-' . $request->postData['action'], $nonce) === true
        ) {
            $isValid = true;
        }

        if (
            isset($request->postData['action'])
            && !isset($request->postData['id'])
            && in_array($request->postData['action'], ['reset', 'reset-default', 'save', 'search'], true)
            && $this->wpFunction->wpVerifyNonce($controllerId . '-' . $request->postData['action'], $nonce) === true
        ) {
            $isValid = true;
        }

        if ($controller instanceof ExtendedRouteValidationInterface) {
            $isValid = $controller->validate($request, $nonce, $isValid);
        }

        return $isValid;
    }
}
