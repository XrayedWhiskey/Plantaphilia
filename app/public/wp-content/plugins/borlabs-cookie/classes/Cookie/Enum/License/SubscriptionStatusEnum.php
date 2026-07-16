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

namespace Borlabs\Cookie\Enum\License;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static SubscriptionStatusEnum ACTIVE()
 * @method static SubscriptionStatusEnum DELETED()
 * @method static SubscriptionStatusEnum PAST_DUE()
 * @method static SubscriptionStatusEnum PAUSED()
 * @method static SubscriptionStatusEnum TRIALING()
 * @method static SubscriptionStatusEnum UNKNOWN()
 */
class SubscriptionStatusEnum extends AbstractEnum
{
    public const ACTIVE = 'active';

    public const DELETED = 'deleted';

    public const PAST_DUE = 'past_due';

    public const PAUSED = 'paused';

    public const TRIALING = 'trialing';

    public const UNKNOWN = 'unknown';
}
