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
 * @method static ValidatorEnum EMAIL()
 * @method static ValidatorEnum NO_VALIDATION()
 * @method static ValidatorEnum URL()
 * @method static ValidatorEnum VALIDATION_REGEX()
 */
class ValidatorEnum extends AbstractEnum
{
    public const EMAIL = 'email';

    public const NO_VALIDATION = 'no-validation';

    public const URL = 'url';

    public const VALIDATION_REGEX = 'validation-regex';
}
