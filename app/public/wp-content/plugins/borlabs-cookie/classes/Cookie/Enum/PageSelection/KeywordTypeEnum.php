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

namespace Borlabs\Cookie\Enum\PageSelection;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static KeywordTypeEnum CONTACT()
 * @method static KeywordTypeEnum IMPRINT()
 * @method static KeywordTypeEnum MAP()
 * @method static KeywordTypeEnum PRIVACY()
 */
class KeywordTypeEnum extends AbstractEnum
{
    public const CONTACT = 'contact';

    public const IMPRINT = 'imprint';

    public const MAP = 'map';

    public const PRIVACY = 'privacy';
}
