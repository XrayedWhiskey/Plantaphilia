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
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

class DataRetentionDto extends AbstractDto
{
    /**
     * @var \Borlabs\Cookie\DtoList\System\KeyValueDtoList. Key: Purpose ID, Value: Retention Period
     */
    public KeyValueDtoList $purposes;

    /**
     * @var \Borlabs\Cookie\DtoList\System\KeyValueDtoList. Key: Special Purpose ID, Value: Retention Period
     */
    public KeyValueDtoList $specialPurposes;

    public int $stdRetention;

    public function __construct(int $stdRetention, KeyValueDtoList $purposes, KeyValueDtoList $specialPurposes)
    {
        $this->stdRetention = $stdRetention;
        $this->purposes = $purposes;
        $this->specialPurposes = $specialPurposes;
    }
}
