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

namespace Borlabs\CookieApi\PhpApi\Component;

use Borlabs\Cookie\System\ContentBlocker\ContentBlockerManager;

final class ContentBlockerPhpApi
{
    private ContentBlockerManager $contentBlockerManager;

    public function __construct(ContentBlockerManager $contentBlockerManager)
    {
        $this->contentBlockerManager = $contentBlockerManager;
    }

    /**
     * Blocks the provided content based on the given parameters.
     *
     * @param string      $content          the content to be blocked
     * @param null|string $url              Optional. If provided, determines the type of Content Blocker based on the URL.
     * @param null|string $contentBlockerId Optional. If provided, specifies the Content Blocker to use by its ID. If `$contentBlockerId` and `$url` are both provided, `$contentBlockerId` takes precedence.
     * @param null|array  $attributes       Optional. Additional attributes to customize the blocking process.
     *
     * @return string the blocked content
     */
    public function blockContent(
        string $content,
        ?string $url = null,
        ?string $contentBlockerId = null,
        ?array $attributes = null
    ): string {
        return $this->contentBlockerManager->handleContentBlocking($content, $url, $contentBlockerId, $attributes);
    }

    /**
     * Detects and blocks iframes in the provided content.
     *
     * @param string $htmlContent the content to be blocked
     *
     * @return string the blocked content
     */
    public function detectAndBlockIframes(string $htmlContent = ''): string
    {
        return $this->contentBlockerManager->detectIframes($htmlContent);
    }
}
