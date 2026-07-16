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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\System\Template\Template;

final class WpNonceUrl
{
    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(Template $template, WpFunction $wpFunction)
    {
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('wpNonceUrl', function (string $actionUrl, string $action, string $name = '_wpnonce') {
                return $this->wpFunction->wpNonceUrl($actionUrl, $action, $name);
            }),
        );
    }
}
