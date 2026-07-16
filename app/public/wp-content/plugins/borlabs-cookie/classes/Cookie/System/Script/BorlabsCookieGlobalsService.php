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

namespace Borlabs\Cookie\System\Script;

final class BorlabsCookieGlobalsService
{
    private array $properties = [];

    public function addProperty(string $property, array $data): bool
    {
        $json = json_encode($data);
        $this->properties[$property] = $json;

        return true;
    }

    public function getInlineJavaScript(): string
    {
        $inlineJavaScript = '<script>if(typeof window.borlabsCookieGlobals === \'undefined\') { window.borlabsCookieGlobals = {}; }';

        foreach ($this->properties as $property => $json) {
            $inlineJavaScript .= "\nwindow.borlabsCookieGlobals['" . $property . "'] = " . $json . ';';
        }
        $inlineJavaScript .= '</script>';

        return $inlineJavaScript;
    }
}
