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

namespace Borlabs\Cookie\Model\ConsentLog;

use Borlabs\Cookie\DtoList\ConsentLog\ServiceGroupConsentDtoList;
use Borlabs\Cookie\Model\AbstractModel;
use DateTimeInterface;

/**
 * The **ConsentLogModel** class is used as a typed object that is passed within the system.
 *
 * @see \Borlabs\Cookie\Repository\ConsentLog\ConsentLogRepository The repository class for this model.
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$id
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$uid
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$cookieVersion
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$consents
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$iabTcfTCString
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$isLatest
 * @see \Borlabs\Cookie\Model\ConsentLog\ConsentLogModel::$stamp
 */
final class ConsentLogModel extends AbstractModel
{
    public ServiceGroupConsentDtoList $consents;

    /**
     * @var int the cookie version to which this consent relates
     */
    public int $cookieVersion;

    public ?string $iabTcfTCString = null;

    /**
     * @var bool `true`: this consent is the most current
     */
    public bool $isLatest;

    /**
     * @var DateTimeInterface Date and time of the consent. Format: Y-m-d H:i:s; Example: 1970-12-31 23:59:59
     */
    public DateTimeInterface $stamp;

    /**
     * @var string unique cookie id
     */
    public string $uid;
}
