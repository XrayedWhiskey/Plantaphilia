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
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Template\Template;
use DateTime;
use DateTimeZone;

final class AdjustTimezone
{
    private Option $option;

    private Template $template;

    public function __construct(Option $option, Template $template)
    {
        $this->option = $option;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('adjustTimezone', function (?DateTime $dateTime = null) {
                if ($dateTime === null) {
                    return null;
                }

                $timeZone = $this->option->getThirdPartyOption('timezone_string', '');
                $gtmOffset = $this->option->getThirdPartyOption('gmt_offset', '');

                if ($timeZone->value !== '') {
                    $dateTime->setTimezone(new DateTimeZone($timeZone->value));
                } elseif ($gtmOffset->value !== '') {
                    $sign = $gtmOffset->value < 0 ? '-' : '+';
                    $absoluteOffset = abs($gtmOffset->value * 100);
                    $formattedOffset = sprintf('%s%04d', $sign, $absoluteOffset);
                    $dateTime->setTimezone(new DateTimeZone($formattedOffset));
                }

                return $dateTime;
            }),
        );
    }
}
