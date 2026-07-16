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

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Dto\System\MessageDto;
use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use WP_CLI;

abstract class AbstractCommand
{
    /**
     * @return null|bool|string
     */
    protected function formatBoolean(?bool $value, string $format)
    {
        if ($format === 'table') {
            if ($value === null) {
                return '-';
            }

            return $value ? 'true' : 'false';
        }

        return $value;
    }

    protected function getBooleanSettingsField(?SettingsFieldDtoList $settingsFieldDtoList, string $settingsFieldKey): ?bool
    {
        if ($settingsFieldDtoList === null) {
            return null;
        }

        $settingsFieldDto = $settingsFieldDtoList->getByKey($settingsFieldKey);

        if ($settingsFieldDto === null) {
            return null;
        }

        if ($settingsFieldDto->value === '1') {
            return true;
        }

        if ($settingsFieldDto->value === '0') {
            return false;
        }

        return null;
    }

    /**
     * @return \WP_CLI\Formatter
     */
    protected function getFormatter(array &$assocArgs, array $defaultFields): WP_CLI\Formatter
    {
        if (!empty($assocArgs['fields'])) {
            if (is_string($assocArgs['fields'])) {
                $fields = explode(',', $assocArgs['fields']);
            } else {
                $fields = $assocArgs['fields'];
            }
        } else {
            $fields = $defaultFields;
        }

        return new \WP_CLI\Formatter($assocArgs, $fields);
    }

    protected function mapSettingsFieldToCliTable(SettingsFieldDto $settingsFieldDto, string $format): array
    {
        return [
            'key' => $settingsFieldDto->key,
            'data-type' => $settingsFieldDto->dataType->value,
            'default-value' => $settingsFieldDto->defaultValue,
            'value' => $settingsFieldDto->value,
        ];
    }

    protected function printMessage(MessageDto $messageDto): void
    {
        if ($messageDto->type === 'error') {
            $type = 'Error';
        } elseif ($messageDto->type === 'success') {
            $type = 'Success';
        } elseif ($messageDto->type === 'info') {
            $type = 'Info';
        } elseif ($messageDto->type === 'warning') {
            $type = 'Warning';
        } elseif ($messageDto->type === 'offer') {
            $type = 'Offer';
        } elseif ($messageDto->type === 'critical') {
            $type = 'Critical';
        } else {
            $type = 'Info';
        }

        WP_CLI::line($type . ': ' . $messageDto->message);
    }

    /**
     * @param \Borlabs\Cookie\Dto\System\MessageDto[] $messageDtos
     */
    protected function printMessages(array $messageDtos): void
    {
        foreach ($messageDtos as $messageDto) {
            $this->printMessage($messageDto);
        }
    }
}
