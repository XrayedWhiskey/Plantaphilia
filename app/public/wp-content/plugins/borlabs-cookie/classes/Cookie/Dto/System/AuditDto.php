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

namespace Borlabs\Cookie\Dto\System;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * The **AuditDto** class is used as a typed object that is passed within the system.
 *
 * It contains the status and message of an audit. In most cases, the message is empty if the audit was successful.
 *
 * @see \Borlabs\Cookie\Dto\System\AuditDto::$success
 * @see \Borlabs\Cookie\Dto\System\AuditDto::$message
 */
final class AuditDto extends AbstractDto
{
    /**
     * @var string status message of the audit
     */
    public string $message;

    /**
     * @var bool if audit was successful the value is true
     */
    public bool $success;

    /**
     * AuditDto constructor.
     *
     * @param bool   $success if audit was successful the value is true
     * @param string $message optional; Default: `''`; Status message of the request
     */
    public function __construct(
        bool $success,
        string $message = ''
    ) {
        $this->success = $success;
        $this->message = $message;
    }
}
