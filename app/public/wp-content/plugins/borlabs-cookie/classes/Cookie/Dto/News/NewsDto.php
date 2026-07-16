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

namespace Borlabs\Cookie\Dto\News;

use Borlabs\Cookie\Dto\AbstractDto;
use DateTime;

/**
 * The **NewsDto** class is used as a typed object that is passed within the system.
 *
 * The object contains news information.
 *
 * @see \Borlabs\Cookie\Dto\News\NewsDto::$language
 * @see \Borlabs\Cookie\Dto\News\NewsDto::$timestamp
 * @see \Borlabs\Cookie\Dto\News\NewsDto::$message
 */
class NewsDto extends AbstractDto
{
    /**
     * @var string Id of the news
     */
    public string $id;

    /**
     * @var string language code
     */
    public string $language;

    /**
     * @var string The text of the news
     */
    public string $message;

    /**
     * @var DateTime Date and time of the news. Format: Y-m-d H:i:s; Example: 1970-12-31 23:59:59
     */
    public DateTime $stamp;

    /**
     * @var string title of the news
     */
    public string $title;

    /**
     * NewsDto constructor.
     *
     * @param string   $id       Id of the news
     * @param string   $language language code
     * @param string   $title    The title of the news
     * @param string   $message  the text of the news
     * @param DateTime $stamp;   Date and time of the news. Format: Y-m-d H:i:s; Example: 1970-12-31 23:59:59
     */
    public function __construct(
        string $id,
        string $language,
        string $title,
        string $message,
        DateTime $stamp
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->message = $message;
        $this->stamp = $stamp;
        $this->title = $title;
    }
}
