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

use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Template\Template;

final class GetPackageUpdateCount
{
    private PackageManager $packageManager;

    private Template $template;

    public function __construct(PackageManager $packageManager, Template $template)
    {
        $this->packageManager = $packageManager;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('getPackageUpdateCount', function (): int {
                return $this->packageManager->getPackageUpdateCount();
            }),
        );
    }
}
