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
use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\System\Template\Template;

final class GenerateRepeatableFormFieldId
{
    private Template $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction(
                'generateRepeatableFormFieldId',
                /**
                 * Generates a unique identifier by concatenating provided parameters with a specific format.
                 *
                 * @param string           $idPrefix            prefix to be used at the beginning of the identifier
                 * @param string           $repeatableFieldsKey the key of the repeatable field to include in the identifier
                 * @param SettingsFieldDto $settingsFieldDto
                 * @param int|string       $index               index value to be appended to the identifier
                 *
                 * @return string a generated unique identifier combining the provided parameters
                 */
                function (
                    string $idPrefix,
                    string $repeatableFieldsKey,
                    object $settingsFieldDto,
                    string $index
                ) {
                    return $idPrefix . '-' . $settingsFieldDto->formFieldCollectionName . '-' . $repeatableFieldsKey . '-' . $index . '-' . $settingsFieldDto->key;
                },
            ),
        );
    }
}
