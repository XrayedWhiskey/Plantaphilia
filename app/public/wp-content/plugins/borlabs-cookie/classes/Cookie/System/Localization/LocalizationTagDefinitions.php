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

namespace Borlabs\Cookie\System\Localization;

use Traversable;

class LocalizationTagDefinitions
{
    /**
     * @var LocalizationTagDefinition[]
     */
    private array $localizationTags = [];

    public function __construct()
    {
        $this->localizationTags[] = new LocalizationTagDefinition(
            'translation-key',
            'translationKeys',
            '#<translation-key id="([a-z0-9\-_]+)">(.*?)</translation-key>#i',
        );
    }

    /**
     * This method should ONLY be called by unit tests.
     */
    public function addWpUnitTestTags(): void
    {
        foreach ($this->localizationTags as $localizationTag) {
            if ($localizationTag->tagName === 'name') {
                return;
            }
        }

        $this->localizationTags[] = new LocalizationTagDefinition(
            'name',
            'names',
            '#<name id="([a-z0-9\-_]+)">(.*?)</name>#i',
        );
        $this->localizationTags[] = new LocalizationTagDefinition(
            'term',
            'terms',
            '#<term id="([a-z0-9\-_]+)">(.*?)</term>#i',
        );
    }

    public function getTagIterator(): Traversable
    {
        foreach ($this->localizationTags as $tag) {
            yield $tag;
        }
    }
}
