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

namespace Borlabs\Cookie\Validator;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Localization\ValidatorLocalizationStrings;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Validator as SupportValidator;
use Borlabs\Cookie\System\Message\MessageManager;

/**
 * Class Validator.
 *
 * @see \Borlabs\Cookie\Support\Validator Used to validate data within programming.
 */
final class Validator
{
    private bool $enableMessages;

    /**
     * @var null|\Borlabs\Cookie\Localization\ValidatorLocalizationStrings|string[]
     */
    private ?array $localization = null;

    private MessageManager $messageManager;

    private bool $valid = true;

    public function __construct(MessageManager $messageManager, bool $enableMessages = false)
    {
        $this->messageManager = $messageManager;
        $this->enableMessages = $enableMessages;
    }

    public function isBoolean(string $fieldName, string $sample): bool
    {
        if (in_array($sample, [
            '0',
            '1',
            '',
        ], true)) {
            return true;
        }

        $this->valid = false;
        $this->addMessage(
            __FUNCTION__,
            [
                'fieldName' => $fieldName,
            ],
        );

        return false;
    }

    /**
     * @param class-string<AbstractEnum> $enumClassName
     */
    public function isEnumValue(string $fieldName, string $sample, string $enumClassName): bool
    {
        if ($enumClassName::hasValue($sample)) {
            return true;
        }

        $this->valid = false;
        $this->addMessage(
            __FUNCTION__,
            [
                'fieldName' => $fieldName,
            ],
        );

        return false;
    }

    public function isHexColor(string $fieldName, string $sample): bool
    {
        if (SupportValidator::isHexColor($sample) !== true) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );

            return false;
        }

        return true;
    }

    public function isIntegerGreaterThan(string $fieldName, string $sample, int $limit): bool
    {
        if (preg_match('/^[0-9]+$/', $sample) === 0 || (int) $sample <= $limit) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                    'limit' => $limit,
                ],
            );

            return false;
        }

        return true;
    }

    public function isMinLengthCertainCharacters(
        string $fieldName,
        string $sample,
        int $minLength,
        string $characterPool
    ): bool {
        if (preg_match('/^[' . $characterPool . ']{' . $minLength . ',}$/', $sample) !== 1) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                    'minLength' => $minLength,
                    'characterPool' => stripslashes($characterPool),
                ],
            );

            return false;
        }

        return true;
    }

    public function isNotEmptyString(string $fieldName, string $sample): bool
    {
        if (strlen($sample) < 1) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );

            return false;
        }

        return true;
    }

    public function isNotReservedWord(string $fieldName, string $sample, array $reservedWordPool): bool
    {
        if (strlen($sample) < 1) {
            $this->valid = false;

            return false;
        }

        if (in_array($sample, $reservedWordPool, true) === true) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );
        }

        return true;
    }

    public function isStringJSON(string $fieldName, string $sample): bool
    {
        if (!\Borlabs\Cookie\Support\Validator::isStringJSON($sample)) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );

            return false;
        }

        return true;
    }

    public function isUniqueKey(
        string $fieldName,
        string $sample,
        string $column,
        RepositoryInterface $modelRepository,
        ?string $language = null
    ): bool {
        if (strlen($sample) < 1) {
            $this->valid = false;

            return false;
        }

        $where = [$column => $sample];

        if ($language !== null) {
            $where['language'] = $language;
        }

        $models = $modelRepository->find($where, [], [0, 1]);

        if ($models !== null && count($models) > 0) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );

            return false;
        }

        return true;
    }

    public function isUrl(string $fieldName, string $sample): bool
    {
        if (filter_var($sample, FILTER_VALIDATE_URL) === false) {
            $this->valid = false;
            $this->addMessage(
                __FUNCTION__,
                [
                    'fieldName' => $fieldName,
                ],
            );

            return false;
        }

        return true;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    private function addMessage(string $stringKey, array $context = []): void
    {
        if ($this->localization === null) {
            $this->localization = ValidatorLocalizationStrings::get();
        }

        if ($this->enableMessages === false) {
            return;
        }

        if (!isset($this->localization[$stringKey])) {
            $this->messageManager->error($stringKey);
        } else {
            $this->messageManager->error(
                Formatter::interpolate(
                    $this->localization[$stringKey],
                    $context,
                ),
            );
        }
    }
}
