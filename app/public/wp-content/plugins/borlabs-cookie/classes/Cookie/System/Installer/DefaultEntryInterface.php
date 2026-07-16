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

namespace Borlabs\Cookie\System\Installer;

use Borlabs\Cookie\Model\AbstractModel;

interface DefaultEntryInterface
{
    /**
     * @param null|string $languageCode The `$languageCode` parameter must have a default value of `null` for
     *                                  backward compatibility reasons. Services like `ProviderService` are integrated
     *                                  into controller classes such as `ProviderController`, which are loaded during
     *                                  the WordPress update routine. As a result, their interfaces cannot have changed
     *                                  requirements. A future solution would be to rename the classes, such as
     *                                  `ProviderDefaultEntries`.
     */
    public function getDefaultModel(?string $languageCode = null): AbstractModel;
}
