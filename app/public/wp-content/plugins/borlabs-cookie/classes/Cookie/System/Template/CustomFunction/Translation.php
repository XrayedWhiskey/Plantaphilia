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
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Template\Template;

final class Translation
{
    private Language $language;

    private Template $template;

    public function __construct(Language $language, Template $template)
    {
        $this->language = $language;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('translation', function (string $key, object $translations) {
                return Searcher::findObject($translations->list, 'language', $this->language->getUserLanguageCode())->{$key}
                    ?? Searcher::findObject($translations->list, 'language', 'en')->{$key}
                    ?? '';
            }),
        );
    }
}
