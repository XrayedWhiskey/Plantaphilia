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

use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;

final class RepeatableFieldCardGenerator
{
    private Template $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function makeCards(
        RepeatableSettingsFieldDtoList $repeatableSettingsFieldDtoList,
        string $idPrefix,
        string $namePrefix
    ): string {
        $preSortedList = [];

        foreach ($repeatableSettingsFieldDtoList->list as $repeatableSettingsField) {
            $preSortedList[$repeatableSettingsField->position][] = $repeatableSettingsField;
        }
        ksort($preSortedList);

        $sortedList = [];

        foreach ($preSortedList as $fieldsWithSamePosition) {
            foreach ($fieldsWithSamePosition as $field) {
                $sortedList[] = $field;
            }
        }

        $html = [];

        foreach ($sortedList as $repeatableSettingsField) {
            $html[] = $this->template->getEngine()->render(
                'system/repeatable-fields-card.html.twig',
                array_merge(
                    (array) $repeatableSettingsField,
                    // TODO BETTER VAR NAMING
                    [
                        'idPrefix' => $idPrefix,
                        'namePrefix' => $namePrefix,
                        'localized' => [
                            'global' => GlobalLocalizationStrings::get(),
                        ],
                    ],
                ),
            );
        }

        return implode('', $html);
    }
}
