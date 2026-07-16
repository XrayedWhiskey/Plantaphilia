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

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\Telemetry\LegalLinkDtoList;

final class LegalLinksPerLanguageListItemDto extends AbstractDto
{
    public string $language;

    public LegalLinkDtoList $legalLinks;

    public function __construct(string $language, LegalLinkDtoList $legalLinks)
    {
        $this->language = $language;
        $this->legalLinks = $legalLinks;
    }
}
