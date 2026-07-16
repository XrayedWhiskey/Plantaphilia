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
use Borlabs\Cookie\System\Template\Template;

final class GenerateRepeatableFormFieldName
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
                'generateRepeatableFormFieldName',
                /**
                 * Generates a form field name by concatenating provided parameters with a specific format.
                 *
                 * @param string     $namePrefix          the prefix to be used at the beginning of the formatted string
                 * @param string     $repeatableFieldsKey a key that is used to identify repeatable fields
                 * @param int|string $index               the index to be included in the formatted string
                 *
                 * @return string the concatenated and formatted string
                 */
                function (
                    string $namePrefix,
                    string $repeatableFieldsKey,
                    object $settingsFieldDto,
                    string $index
                ) {
                    return $namePrefix . '[' . $settingsFieldDto->formFieldCollectionName . '][' . $repeatableFieldsKey . '][' . $index . '][' . $settingsFieldDto->key . ']';
                },
            ),
        );
    }
}
