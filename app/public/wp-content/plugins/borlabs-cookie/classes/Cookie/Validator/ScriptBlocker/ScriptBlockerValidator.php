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

namespace Borlabs\Cookie\Validator\ScriptBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerCreateEditLocalizationStrings;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

class ScriptBlockerValidator
{
    private ScriptBlockerRepository $scriptBlockerRepository;

    private Validator $validator;

    private WpFunction $wpFunction;

    public function __construct(
        MessageManager $messageManager,
        ScriptBlockerRepository $scriptBlockerRepository,
        WpFunction $wpFunction
    ) {
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->validator = new Validator($messageManager, true);
        $this->wpFunction = $wpFunction;
    }

    public function isValid(array $postData): bool
    {
        $localization = ScriptBlockerCreateEditLocalizationStrings::get();

        if ($postData['id'] === '-1') {
            $this->validator->isMinLengthCertainCharacters(
                $localization['field']['key'],
                $postData['key'],
                3,
                'a-z\-\_',
            );
            $this->validator->isUniqueKey(
                $localization['field']['key'],
                $postData['key'],
                'key',
                $this->scriptBlockerRepository,
            );
        }
        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name'] ?? '');
        $this->validator = $this->wpFunction->applyFilter('borlabsCookie/scriptBlocker/validate', $this->validator, $postData);

        return $this->validator->isValid();
    }
}
