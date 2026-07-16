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

namespace Borlabs\Cookie\Localization\ConsentLog;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class ConsentLogDetailsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'notFound' => _x(
                    'The <translation-key id="Consent-Log">Consent Log</translation-key> could not be found.',
                    'Backend / Consent Log / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    '<translation-key id="Consent-Logs">Consent Logs</translation-key>',
                    'Backend / Consent Log / Breadcrumb',
                    'borlabs-cookie',
                ),
                'details' => _x(
                    'Details',
                    'Backend / Consent Log / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'consents' => _x(
                    'Consents',
                    'Backend / Consent Log / Field',
                    'borlabs-cookie',
                ),
                'cookieVersion' => _x(
                    'Cookie Version',
                    'Backend / Consent Log / Field',
                    'borlabs-cookie',
                ),
                'iabTcfTCString' => _x(
                    'IAB TCF TC String',
                    'Backend / Consent Log / Label',
                    'borlabs-cookie',
                ),
                'isLatest' => _x(
                    'Is Latest Consent',
                    'Backend / Consent Log / Label',
                    'borlabs-cookie',
                ),
                'uid' => _x(
                    'UID',
                    'Backend / Consent Log / Label',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'cookieVersion' => _x(
                    'A version number is assigned to the cookie of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. It is used to ask the visitor for consent again when changes are made to the cookies. If the version number in the cookie differs from the current version number, the consent dialog appears to the visitor.',
                    'Backend / Consent Log / Hint',
                    'borlabs-cookie',
                ),
                'iabTcfTCString' => _x(
                    'The <translation-key id="TC-String">TC String</translation-key> is an encoded depiction of user consent choices, generated through interactions with <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. It informs publishers, advertisers, and ad tech vendors of the permitted uses of user data, based on users\' consent decisions.',
                    'Backend / Consent Log / Hint',
                    'borlabs-cookie',
                ),
                'uid' => _x(
                    'The <translation-key id="UID">UID</translation-key> is a unique identifier of the user, generated through interactions with <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Consent Log / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
