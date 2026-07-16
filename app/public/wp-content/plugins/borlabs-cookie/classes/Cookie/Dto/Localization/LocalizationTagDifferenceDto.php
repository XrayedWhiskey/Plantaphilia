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

class LocalizationTagDifferenceDto extends AbstractDto
{
    /**
     * @var class-string
     */
    public string $classNameFirst;

    /**
     * @var class-string
     */
    public string $classNameSecond;

    public string $contentFirst;

    public string $contentSecond;

    public string $id;

    public function __construct(
        string $id,
        string $classNameFirst,
        string $contentFirst,
        string $classNameSecond,
        string $contentSecond
    ) {
        $this->id = $id;
        $this->classNameFirst = $classNameFirst;
        $this->contentFirst = $contentFirst;
        $this->classNameSecond = $classNameSecond;
        $this->contentSecond = $contentSecond;
    }
}
