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

namespace Borlabs\Cookie\Dto\ApiClient;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * The **ServiceResponseDto** class is used as a typed object that is passed within the system.
 *
 * It contains the status and data of the response from a request to the Borlabs servers.
 *
 * @see \Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto::$success
 * @see \Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto::$statusCode
 * @see \Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto::$messageCode
 * @see \Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto::$data
 */
final class ServiceResponseDto extends AbstractDto
{
    /**
     * @var object service response data
     */
    public object $data;

    /**
     * @var string a code that can sometimes be translated into a localized message
     */
    public string $messageCode;

    /**
     * @var int HTTP status code
     */
    public int $statusCode;

    /**
     * @var bool if request was successful the value is true
     */
    public bool $success;

    /**
     * ServiceResponseDto constructor.
     *
     * @param bool   $success     if request was successful the value is true
     * @param int    $statusCode  HTTP status code
     * @param string $messageCode a code that can sometimes be translated into a localized message
     * @param object $data        service response data
     */
    public function __construct(
        bool $success,
        int $statusCode,
        string $messageCode,
        object $data
    ) {
        $this->success = $success;
        $this->statusCode = $statusCode;
        $this->messageCode = $messageCode;
        $this->data = $data;
    }
}
