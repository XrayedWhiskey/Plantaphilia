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
use Borlabs\Cookie\System\Template\FieldGenerator;
use Borlabs\Cookie\System\Template\Template;

final class MakeFieldWithoutLabel
{
    private FieldGenerator $fieldGenerator;

    private Template $template;

    public function __construct(FieldGenerator $fieldGenerator, Template $template)
    {
        $this->fieldGenerator = $fieldGenerator;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction(
                'makeFieldWithoutLabel',
                function (
                    object $settingsField,
                    string $id,
                    string $name
                ) {
                    return $this->fieldGenerator->makeFieldWithoutLabel(
                        $settingsField,
                        $id,
                        $name,
                    );
                },
            ),
        );
    }
}
