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

namespace Borlabs\Cookie\System\Template;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dependencies\Twig\Environment;
use Borlabs\Cookie\Dependencies\Twig\Loader\ArrayLoader;
use Borlabs\Cookie\Dependencies\Twig\Loader\FilesystemLoader;

/**
 * Singleton class Template.
 *
 * The **Template** class is wrapper for the Twig template engine.
 *
 * Use {@see \Borlabs\Cookie\System\WordPressAdminDriver\Template::getEngine()} to access the Twig instance.
 * The instance is set up to load template files from ./templates.
 *
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\Template::getArrayLoadedEngine() Use this parse templates that are
 *     stored in a string.
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\Template::getEngine() Returns the Twig instance.
 */
final class Template
{
    private Container $container;

    private bool $isInitialized = false;

    private Environment $twig;

    private WpFunction $wpFunction;

    public function __construct(Container $container, WpFunction $wpFunction)
    {
        $this->container = $container;
        $this->wpFunction = $wpFunction;

        $this->twig = new Environment(
            new FilesystemLoader(BORLABS_COOKIE_PLUGIN_PATH . '/templates'),
            [
                'debug' => true,
            ],
        );

        $timezone = $this->wpFunction->wpTimezone();

        if ($timezone) {
            $this->twig->getExtension(\Borlabs\Cookie\Dependencies\Twig\Extension\CoreExtension::class)->setTimezone($timezone);
        }

        $this->twig->addExtension(new \Borlabs\Cookie\Dependencies\Twig\Extension\DebugExtension());
    }

    /**
     * With this method you can parse templates that are stored in a string.
     *
     * Example:
     * <code>
     * $this->template->getArrayLoadedEngine(
     *      'myTemplateInAString',
     *      'H-H-Hello there. {{ name }}...'
     * )->render(
     *      'myTemplateInAString',
     *      ['name' => 'General Kenobi']
     * );
     * </code>
     */
    public function getArrayLoadedEngine(string $name, string $template): Environment
    {
        return new Environment(
            new ArrayLoader([
                $name => $template,
            ]),
        );
    }

    /**
     * Returns the Twig instance which is set up to load template files from ./templates.
     *
     * Example:
     * <code>
     * $this->template->render(
     *      'dashboard/dashboard.html.twig',
     *      ['foo' => 'bar']
     * );
     * </code>
     */
    public function getEngine(): Environment
    {
        $this->registerCustomTwigFunctions();

        return $this->twig;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    private function registerCustomTwigFunctions()
    {
        if ($this->isInitialized) {
            return;
        }

        $directory = __DIR__ . '/CustomFunction/';
        $files = glob($directory . '*.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $classNameWithNamespace = '\\' . __NAMESPACE__ . '\\CustomFunction\\' . $className;

            if (class_exists($classNameWithNamespace)) {
                $this->container->get($classNameWithNamespace)->register();
            }
        }

        $this->isInitialized = true;
    }
}
