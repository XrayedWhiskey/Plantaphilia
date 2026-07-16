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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\Dto\System\MessageDto;
use Borlabs\Cookie\Localization\License\LicenseLocalizationStrings;
use Borlabs\Cookie\System\Template\Template;

final class GetLicenseAlertMessage
{
    private LicenseLocalizationStrings $licenseLocalizationStrings;

    private Template $template;

    public function __construct(LicenseLocalizationStrings $licenseLocalizationStrings, Template $template)
    {
        $this->licenseLocalizationStrings = $licenseLocalizationStrings;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('getLicenseAlertMessage', function (string $alertKey) {
                if (!isset($this->licenseLocalizationStrings::get()['alert'][$alertKey])) {
                    return '';
                }

                return $this->template->getEngine()->render(
                    'message/messages.html.twig',
                    [
                        'messages' => [
                            new MessageDto($this->licenseLocalizationStrings::get()['alert'][$alertKey], 'error'),
                        ],
                        'noWrapper' => true,
                    ],
                );
            }),
        );
    }
}
