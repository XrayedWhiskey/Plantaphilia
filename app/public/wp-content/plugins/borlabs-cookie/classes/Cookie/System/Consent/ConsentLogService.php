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

namespace Borlabs\Cookie\System\Consent;

use Borlabs\Cookie\Dto\ConsentLog\ServiceGroupConsentDto;
use Borlabs\Cookie\DtoList\ConsentLog\ServiceGroupConsentDtoList;
use Borlabs\Cookie\Exception\RestEndpoint\InvalidUserRestInputException;
use Borlabs\Cookie\Model\ConsentLog\ConsentLogModel;
use Borlabs\Cookie\Repository\ConsentLog\ConsentLogRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Option\Option;
use DateTime;

final class ConsentLogService
{
    private ConsentLogRepository $consentLogRepository;

    private GeneralConfig $generalConfig;

    private Option $option;

    private ServiceGroupRepository $serviceGroupRepository;

    public function __construct(
        ConsentLogRepository $consentLogRepository,
        GeneralConfig $generalConfig,
        Option $option,
        ServiceGroupRepository $serviceGroupRepository
    ) {
        $this->consentLogRepository = $consentLogRepository;
        $this->generalConfig = $generalConfig;
        $this->option = $option;
        $this->serviceGroupRepository = $serviceGroupRepository;
    }

    /**
     * @param mixed $languageCode TODO: Define type
     *
     * @throws \Borlabs\Cookie\Exception\RestEndpoint\InvalidUserRestInputException
     */
    public function add(
        $languageCode,
        string $uid,
        int $cookieVersion,
        string $borlabsCookieConsentString,
        ?string $iabTcfTCString = null
    ) {
        if ($uid === 'anonymous') {
            return;
        }

        $consentLog = new ConsentLogModel();
        $consentLog->cookieVersion = $cookieVersion;
        $consentLog->consents = $this->getValidatedServiceGroupConsentList($languageCode, $borlabsCookieConsentString);
        $consentLog->iabTcfTCString = $iabTcfTCString !== '' ? $iabTcfTCString : null;
        $consentLog->stamp = new DateTime();
        $consentLog->uid = $uid;

        $this->consentLogRepository->insertAsLatestConsent($consentLog);
    }

    public function cleanUp()
    {
        $this->consentLogRepository->deleteAll($this->generalConfig->get()->cookieLifetime);
    }

    public function getHistory(string $uid): array
    {
        $consentLogs = $this->consentLogRepository->getUidHistory($uid);
        $consentHistory = [];

        foreach ($consentLogs as $consentLogModel) {
            $consentHistory[] = [
                'consents' => $consentLogModel->consents->list,
                'iabTcfTCString' => $consentLogModel->iabTcfTCString,
                'isLatest' => $consentLogModel->isLatest,
                'stamp' => Formatter::timestamp(
                    $consentLogModel->stamp->getTimestamp(),
                    'Y-m-d',
                    'H:i:s',
                ),
                'uid' => $consentLogModel->uid,
                'version' => $consentLogModel->cookieVersion,
            ];
        }

        return $consentHistory;
    }

    /**
     * @return array<ConsentLogModel>
     */
    public function getLast(int $rowCount, bool $ignoreCookieVersion = false): array
    {
        $cookieVersion = $this->option->getGlobal('CookieVersion', 1)->value;
        $where = ['isLatest' => 1];

        if (!$ignoreCookieVersion) {
            $where['cookieVersion'] = $cookieVersion;
        }

        return $this->consentLogRepository->find($where, [], [0, $rowCount]);
    }

    /**
     * The method verifies and removes any invalid consents related to services and service groups.
     *
     * @throws InvalidUserRestInputException
     */
    public function getValidatedServiceGroupConsentList(string $languageCode, string $borlabsCookieConsentString): ServiceGroupConsentDtoList
    {
        // [serviceGroupKey] => [serviceKey, serviceKey, ...]
        $serviceGroupsWithServiceKeys = array_column(
            array_map(
                fn ($serviceGroup) => [
                    'key' => $serviceGroup->key,
                    'services' => array_map(
                        fn ($service) => $service->key,
                        $serviceGroup->services,
                    ),
                ],
                $this->serviceGroupRepository->getAllActiveOfLanguage($languageCode, true),
            ),
            'services',
            'key',
        );

        /**
         * @var array $consents
         *
         * Example
         * <code>
         * [
         *     (object) [
         *         'id' => serviceGroupKey,
         *         'services' => [serviceKey, serviceKey, ...],
         *     ],
         *     (object) [...],
         * ]
         * </code>
         */
        $consents = json_decode($borlabsCookieConsentString);

        if (!is_array($consents)) {
            throw new InvalidUserRestInputException();
        }

        return new ServiceGroupConsentDtoList(
            array_filter(
                array_map(
                    // Only add service group key if service group exists
                    fn ($serviceGroupConsent) => isset($serviceGroupsWithServiceKeys[$serviceGroupConsent->id])
                        ? new ServiceGroupConsentDto(
                            $serviceGroupConsent->id,
                            $this->filterServiceConsents(
                                $serviceGroupConsent->services,
                                $serviceGroupsWithServiceKeys[$serviceGroupConsent->id],
                            ),
                        ) : null,
                    $consents,
                ),
                // Filter out null values and service groups without services
                fn ($serviceGroup) => $serviceGroup !== null && count($serviceGroup->services),
            ),
        );
    }

    /**
     * @param array $serviceConsents      [serviceKey, serviceKey, ...]
     * @param array $serviceGroupServices [serviceKey, serviceKey, ...]
     *
     * @return array [serviceKey, serviceKey, ...]
     */
    private function filterServiceConsents(array $serviceConsents, array $serviceGroupServices): array
    {
        /*
        * We require an array with a starting index of 0. Failure to do so may result
        * in an array containing an entry with an index of 1, which would be converted into an object,
        * not the array we intended. Therefore array_values is called.
        * */
        return array_values(
            array_filter(
                array_map(
                    // Only add service key if service exists in service group
                    fn ($serviceKey) => in_array($serviceKey, $serviceGroupServices, true)
                        ? $serviceKey
                        : null,
                    $serviceConsents,
                ),
                // Filter out null values
                fn ($serviceKey) => $serviceKey !== null,
            ),
        );
    }
}
