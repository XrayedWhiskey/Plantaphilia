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

namespace Borlabs\Cookie\Validator\Import;

use Borlabs\Cookie\Localization\ImportExport\ImportExportLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class DialogAppearanceValidator.
 */
final class ImportValidator
{
    /**
     * @var \Borlabs\Cookie\Validator\Validator
     */
    private $validator;

    public function __construct(MessageManager $message)
    {
        $this->validator = new Validator($message, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = ImportExportLocalizationStrings::get();
        $l = $localization['field'];

        $this->validator->isNotEmptyString(
            $l['importData'],
            $postData['importData'] ?? '',
        );
        $this->validator->isStringJSON(
            $l['importData'],
            $postData['importData'] ?? '',
        );

        return $this->validator->isValid();
    }
}
