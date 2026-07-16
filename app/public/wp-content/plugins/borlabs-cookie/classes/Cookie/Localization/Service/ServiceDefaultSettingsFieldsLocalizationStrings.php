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

namespace Borlabs\Cookie\Localization\Service;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ServiceCreateLocalizationStrings** class contains various localized strings.
 */
final class ServiceDefaultSettingsFieldsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Fields
            'field' => [
                'asynchronous-opt-out-code' => _x(
                    'Asynchronous Opt-out Code',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'block-cookies-before-consent' => _x(
                    'Block Cookies Before Consent',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'disable-code-execution' => _x(
                    'Disable Code Execution',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'prioritize' => _x(
                    '<translation-key id="Prioritize">Prioritize</translation-key>',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'asynchronous-opt-out-code' => _x(
                    'The <translation-key id="Opt-out-Code">Opt-out Code</translation-key> contains asynchronous JavaScript code that needs to executed to finish the Opt-out.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'block-cookies-before-consent' => _x(
                    'If enabled, <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> tries to block cookies with the names from <translation-key id="Cookie-Name">Cookie Name</translation-key> until consent is given.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'disable-code-execution' => _x(
                    'If enabled, this <translation-key id="Service">Service</translation-key> refrains from executing any JavaScript code. This feature can be valuable if you solely require the consent of the <translation-key id="Service">Service</translation-key> and intend to execute code through a tag management system (TMS).',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'prioritize' => _x(
                    'The <translation-key id="Opt-in-Code">Opt-in Code</translation-key> is loaded in <strong><em>&lt;head&gt;</em></strong> and is executed before the page is fully loaded.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
