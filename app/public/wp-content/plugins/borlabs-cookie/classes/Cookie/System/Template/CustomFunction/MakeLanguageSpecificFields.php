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
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Template\FieldGenerator;
use Borlabs\Cookie\System\Template\Template;

final class MakeLanguageSpecificFields
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
                'makeLanguageSpecificFields',
                function (
                    object $languageSpecificSetupSettingsFieldsList,
                    string $languageCode,
                    ?string $idPrefix = null,
                    ?string $idName = null
                ): string {
                    $settingsFieldsList = Searcher::findObject($languageSpecificSetupSettingsFieldsList->list, 'language', $languageCode);

                    if ($settingsFieldsList === null) {
                        $settingsFieldsList = Searcher::findObject($languageSpecificSetupSettingsFieldsList->list, 'language', 'en');
                    }

                    if (isset($settingsFieldsList->settingsFields)) {
                        return $this->fieldGenerator->makeFields(
                            $settingsFieldsList->settingsFields,
                            SettingsFieldVisibilityEnum::EDIT_ONLY(),
                            $idPrefix,
                            $idName,
                        );
                    }

                    return '';
                },
            ),
        );
    }
}
