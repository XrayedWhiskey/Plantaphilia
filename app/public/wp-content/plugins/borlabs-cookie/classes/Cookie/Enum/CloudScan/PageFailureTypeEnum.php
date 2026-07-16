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

namespace Borlabs\Cookie\Enum\CloudScan;

use Borlabs\Cookie\Enum\AbstractEnum;

class PageFailureTypeEnum extends AbstractEnum
{
    /**
     * The scanner got an authentication problem. (e.g. website requires basic auth).
     */
    public const AUTHENTICATION = 'authentication';

    /**
     * The scanner had problems with the connectivity while trying to access the page.
     */
    public const CONNECTIVITY = 'connectivity';

    /**
     * Scanner tried to access the page, but the DNS resolution failed.
     */
    public const NAME_NOT_RESOLVED = 'nameNotResolved';

    /**
     * The scanner did not send back the scan results within the timeout in config "scanner.scan_page_scanning_timeout_in_seconds".
     */
    public const NO_ANSWER_FROM_SCANNER = 'noAnswerFromScanner';

    /**
     * Any other error from the Scanner that has no specific type.
     * Note: If this happens often, check to logs and add new failure type to Scanner.
     */
    public const OTHER = 'other';

    /**
     * The scanner got an SSL error while trying to access the page.
     */
    public const SSL_ERROR = 'sslError';

    /**
     * The scanner got a timeout while trying to access the page.
     */
    public const TIMEOUT = 'timeout';

    /**
     * The scanner was redirected too many times while trying to access the page.
     */
    public const TOO_MANY_REDIRECTS = 'tooManyRedirects';
}
