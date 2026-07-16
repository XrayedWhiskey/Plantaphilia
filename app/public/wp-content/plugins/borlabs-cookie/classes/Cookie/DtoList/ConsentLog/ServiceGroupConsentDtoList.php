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

namespace Borlabs\Cookie\DtoList\ConsentLog;

use Borlabs\Cookie\Dto\ConsentLog\ServiceGroupConsentDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ServiceGroupConsentDto>
 */
final class ServiceGroupConsentDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ServiceGroupConsentDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $serviceGroupConsentLogList = null
    ) {
        parent::__construct($serviceGroupConsentLogList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $serviceGroupConsentData) {
            $serviceGroupConsents = new ServiceGroupConsentDto(
                $serviceGroupConsentData->key,
                $serviceGroupConsentData->services,
            );
            $list[$key] = $serviceGroupConsents;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $serviceGroupConsents) {
            $list[$key] = ServiceGroupConsentDto::prepareForJson($serviceGroupConsents);
        }

        return $list;
    }
}
