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

namespace Borlabs\Cookie\Dto\System;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * The **RequestDto** class is used as a typed object that is passed within the system.
 *
 * Used by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} to collect the data from
 * $_POST and $_GET into one object to pass to the loaded controller's route method.
 *
 * @see \Borlabs\Cookie\Dto\System\RequestDto::$getData
 * @see \Borlabs\Cookie\Dto\System\RequestDto::$postData
 * @see \Borlabs\Cookie\Dto\System\RequestDto::$serverData
 */
final class RequestDto extends AbstractDto
{
    /**
     * @var array<array<string>|string> contains data
     */
    public array $getData;

    /**
     * @var array<array<string>|string> contains data
     */
    public array $postData;

    /**
     * @var array<string> contains data
     */
    public array $serverData;

    /**
     * RequestDto constructor.
     *
     * @param array<string> $postData   contains $_POST data
     * @param array<string> $getData    contains $_GET data
     * @param array<string> $serverData contains $_SERVER data
     */
    public function __construct(array $postData, array $getData, array $serverData)
    {
        $this->getData = $getData;
        $this->postData = $postData;
        $this->serverData = $serverData;
    }
}
