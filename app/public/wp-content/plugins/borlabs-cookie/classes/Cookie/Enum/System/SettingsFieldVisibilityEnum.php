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

namespace Borlabs\Cookie\Enum\System;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static SettingsFieldVisibilityEnum EDIT_AND_SETUP()
 * @method static SettingsFieldVisibilityEnum EDIT_ONLY()
 * @method static SettingsFieldVisibilityEnum SETUP_ONLY()
 */
class SettingsFieldVisibilityEnum extends AbstractEnum
{
    public const EDIT_AND_SETUP = 'edit-and-setup';

    public const EDIT_ONLY = 'edit-only';

    public const SETUP_ONLY = 'setup-only';
}
