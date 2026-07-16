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

namespace Borlabs\Cookie\System\Mail;

use Borlabs\Cookie\Adapter\WpFunction;

final class MailService
{
    private WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    public function sendMail(string $to, string $subject, string $body): bool
    {
        return $this->wpFunction->wpMail(
            $to,
            $subject,
            $body,
            [
                'Content-Type: text/html; charset=UTF-8',
            ],
        );
    }
}
