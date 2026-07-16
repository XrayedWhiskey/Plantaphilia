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

namespace Borlabs\Cookie\Dto\IabTcf;

use Borlabs\Cookie\Dto\AbstractDto;

class VendorUrlsDto extends AbstractDto
{
    public string $language;

    public ?string $legitimateInterestClaim;

    public ?string $privacy;

    public function __construct(
        string $language,
        ?string $legitimateInterestClaim = null,
        ?string $privacy = null
    ) {
        $this->language = $language;
        $this->legitimateInterestClaim = $legitimateInterestClaim;
        $this->privacy = $privacy;
    }
}
