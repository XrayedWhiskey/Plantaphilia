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

namespace Borlabs\Cookie\Dto\Config;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * Class AbstractConfigDto.
 *
 * Each config object MUST extend the **AbstractConfigDto** to work with the **AbstractConfigManager** or **AbstractConfigManagerWithLanguage**.
 *
 * @see \Borlabs\Cookie\System\Config\AbstractConfigManager
 * @see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage
 */
#[\AllowDynamicProperties]
abstract class AbstractConfigDto extends AbstractDto
{
}
