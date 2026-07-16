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

namespace Borlabs\Cookie\Dto\Localization;

use Borlabs\Cookie\Dto\AbstractDto;

class LocalizationTagDifferenceInLocalizationDto extends AbstractDto
{
    public string $contentFirst;

    public string $contentSecond;

    public string $contextFirst;

    public string $contextSecond;

    public string $id;

    public string $textFirst;

    public string $textSecond;

    public function __construct(
        string $id,
        string $contextFirst,
        string $textFirst,
        string $contentFirst,
        string $contextSecond,
        string $textSecond,
        string $contentSecond
    ) {
        $this->id = $id;
        $this->contextFirst = $contextFirst;
        $this->contentFirst = $contentFirst;
        $this->textFirst = $textFirst;
        $this->contextSecond = $contextSecond;
        $this->contentSecond = $contentSecond;
        $this->textSecond = $textSecond;
    }
}
