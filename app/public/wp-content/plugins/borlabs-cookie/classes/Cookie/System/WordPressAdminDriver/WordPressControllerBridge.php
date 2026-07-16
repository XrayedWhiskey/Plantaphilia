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

/**
 * The **WordPressBridge** class serves as bridge between the WordPress system and Borlabs Cookie.
 *
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\WordPressAdminInit::addMenu
 */
final class WordPressControllerBridge
{
    private ControllerManager $controllerManager;

    public function __construct(
        ControllerManager $controllerManager
    ) {
        $this->controllerManager = $controllerManager;
    }

    /**
     * This method calls {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and passes the
     * requested module class.
     *
     * @see \Borlabs\Cookie\System\WordPressAdminDriver\WordPressAdminInit::addMenu()
     * @see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()
     *
     * @param string        $moduleClass The controller class located in Controller/, e.g.
     *                                   Dashboard\DashboardController
     * @param array<string> $args        is not used
     */
    public function __call(string $moduleClass, array $args): void
    {
        $this->controllerManager->load($moduleClass);
    }
}
