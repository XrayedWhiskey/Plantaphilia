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

namespace Borlabs\Cookie\System\ConsentStatistic;

use Borlabs\Cookie\DtoList\ConsentLog\ServiceGroupConsentDtoList;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByDayEntryModel;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByDayGroupedByServiceGroupEntryModel;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByHourEntryModel;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByHourGroupedByServiceGroupEntryModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByDayGroupedByServiceGroupRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByDayRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByHourGroupedByServiceGroupRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByHourRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\System\Option\Option;
use DateInterval;
use DateTime;
use DateTimeImmutable;

class ConsentStatisticService
{
    private consentStatisticByDayGroupedByServiceGroupRepository $consentStatisticByDayGroupedByServiceGroupRepository;

    private ConsentStatisticByDayRepository $consentStatisticByDayRepository;

    private consentStatisticByHourGroupedByServiceGroupRepository $consentStatisticByHourGroupedByServiceGroupRepository;

    private ConsentStatisticByHourRepository $consentStatisticByHourRepository;

    private Option $option;

    public function __construct(
        ConsentStatisticByDayGroupedByServiceGroupRepository $consentStatisticByDayGroupedByServiceGroupRepository,
        ConsentStatisticByDayRepository $consentStatisticByDayRepository,
        ConsentStatisticByHourGroupedByServiceGroupRepository $consentStatisticByHourGroupedByServiceGroupRepository,
        ConsentStatisticByHourRepository $consentStatisticByHourRepository,
        Option $option
    ) {
        $this->consentStatisticByDayGroupedByServiceGroupRepository = $consentStatisticByDayGroupedByServiceGroupRepository;
        $this->consentStatisticByDayRepository = $consentStatisticByDayRepository;
        $this->consentStatisticByHourGroupedByServiceGroupRepository = $consentStatisticByHourGroupedByServiceGroupRepository;
        $this->consentStatisticByHourRepository = $consentStatisticByHourRepository;
        $this->option = $option;
    }

    public function add(
        ServiceGroupConsentDtoList $consents,
        string $uid,
        int $cookieVersion,
        ?DateTimeImmutable $date = null
    ) {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        foreach ($consents->list as $serviceGroupData) {
            foreach ($serviceGroupData->services as $service) {
                $model = new ConsentStatisticByHourEntryModel();
                $model->serviceGroupKey = $serviceGroupData->key;
                $model->serviceKey = $service;
                $model->cookieVersion = $cookieVersion;
                $model->count = 1;
                $model->date = $date;
                $model->hour = (int) $date->format('H');
                $model->isAnonymous = $uid === 'anonymous';

                $this->consentStatisticByHourRepository->insertOrIncrementCount($model);
            }

            $model = new ConsentStatisticByHourGroupedByServiceGroupEntryModel();
            $model->serviceGroupKey = $serviceGroupData->key;
            $model->cookieVersion = $cookieVersion;
            $model->count = 1;
            $model->date = $date;
            $model->hour = (int) $date->format('H');
            $model->isAnonymous = $uid === 'anonymous';

            $this->consentStatisticByHourGroupedByServiceGroupRepository->insertOrIncrementCount($model);
        }
    }

    public function aggregateHourEntries()
    {
        // Significance of fields is (left is most significant):
        // Cookie Version > Date > is Anonymous > Service Group Key > Service Key

        // 1. Try to get all new data, and only then delete today's data, just in case something goes wrong.
        $hourEntries = $this->consentStatisticByHourRepository->getAll();
        $preparedHourEntries = [];

        foreach ($hourEntries as $hourEntry) {
            // Use this array structure:
            // Date > Service Group Key > Service Key > Cookie Version > is Anonymous = count
            // We can pre-aggregate counts here, and easily convert it to models below.
            if (!isset($preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous])) {
                $preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous] = 0;
            }

            $preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous] += $hourEntry->count;
        }

        $groupedHourEntries = $this->consentStatisticByHourGroupedByServiceGroupRepository->getAll();
        $preparedGroupedHourEntries = [];

        foreach ($groupedHourEntries as $groupedHourEntry) {
            // Use this array structure:
            // Date > Service Group Key > Cookie Version > is Anonymous = count
            // We can pre-aggregate counts here, and easily convert it to models below.
            if (!isset($preparedGroupedHourEntries[$groupedHourEntry->date->format('Y-m-d')][$groupedHourEntry->serviceGroupKey][$groupedHourEntry->cookieVersion][$groupedHourEntry->isAnonymous])) {
                $preparedGroupedHourEntries[$groupedHourEntry->date->format('Y-m-d')][$groupedHourEntry->serviceGroupKey][$groupedHourEntry->cookieVersion][$groupedHourEntry->isAnonymous] = 0;
            }

            $preparedGroupedHourEntries[$groupedHourEntry->date->format('Y-m-d')][$groupedHourEntry->serviceGroupKey][$groupedHourEntry->cookieVersion][$groupedHourEntry->isAnonymous] += $groupedHourEntry->count;
        }

        // 2. Delete all today's entries.
        // Actually, these could stay, but if we had a bug, this at least allows us to have correct statistics for today.
        /** @var AbstractRepository[] $aggregateRepositories Each must have a `date` column. */
        $aggregateRepositories = [
            $this->consentStatisticByDayRepository,
            $this->consentStatisticByDayGroupedByServiceGroupRepository,
        ];

        foreach ($aggregateRepositories as $aggregateRepository) {
            $dayEntries = $aggregateRepository->find(
                [
                    new BinaryOperatorExpression(
                        new ModelFieldNameExpression('date'),
                        '=',
                        new LiteralExpression((new DateTime())->format('Y-m-d')),
                    ),
                ],
            );

            foreach ($dayEntries as $dayEntry) {
                $aggregateRepository->delete($dayEntry);
            }
        }

        // 3. Add day entries
        foreach ($preparedGroupedHourEntries as $date => $dateEntries) {
            foreach ($dateEntries as $serviceGroupKey => $serviceGroupKeyEntries) {
                foreach ($serviceGroupKeyEntries as $cookieVersion => $cookieVersionEntries) {
                    foreach ($cookieVersionEntries as $isAnonymous => $count) {
                        $model = new ConsentStatisticByDayGroupedByServiceGroupEntryModel();
                        $model->cookieVersion = $cookieVersion;
                        $model->date = new DateTime($date);
                        $model->count = $count;
                        $model->isAnonymous = (bool) $isAnonymous;
                        $model->serviceGroupKey = $serviceGroupKey;
                        $this->consentStatisticByDayGroupedByServiceGroupRepository->insertOrIncrementCount(
                            $model,
                            $count,
                        );
                    }
                }
            }
        }

        foreach ($preparedHourEntries as $date => $dateEntries) {
            foreach ($dateEntries as $serviceGroupKey => $serviceGroupKeyEntries) {
                foreach ($serviceGroupKeyEntries as $service => $serviceEntries) {
                    foreach ($serviceEntries as $cookieVersion => $cookieVersionEntries) {
                        foreach ($cookieVersionEntries as $isAnonymous => $count) {
                            $model = new ConsentStatisticByDayEntryModel();
                            $model->cookieVersion = $cookieVersion;
                            $model->count = $count;
                            $model->date = new DateTime($date);
                            $model->isAnonymous = (bool) $isAnonymous;
                            $model->serviceGroupKey = $serviceGroupKey;
                            $model->serviceKey = $service;

                            $this->consentStatisticByDayRepository->insertOrIncrementCount(
                                $model,
                                $count,
                            );
                        }
                    }
                }
            }
        }

        // 4. Delete all hour entries older than today
        $today = (new DateTime())->setTime(0, 0);

        foreach ($hourEntries as $hourEntry) {
            if ($hourEntry->date < $today) {
                $this->consentStatisticByHourRepository->delete($hourEntry);
            }
        }

        foreach ($groupedHourEntries as $groupedHourEntry) {
            if ($groupedHourEntry->date < $today) {
                $this->consentStatisticByHourGroupedByServiceGroupRepository->delete($groupedHourEntry);
            }
        }
    }

    /**
     * @return array<ConsentStatisticByDayEntryModel>
     */
    public function getLastDays(int $days): array
    {
        $dateTime = (new DateTime())->setTime(0, 0)->sub(DateInterval::createFromDateString($days . ' days'));

        return $this->consentStatisticByDayRepository->getAll(
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('date'),
                    '>=',
                    new LiteralExpression($dateTime->format('Y-m-d')),
                ),
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('cookieVersion'),
                    '=',
                    new LiteralExpression($this->option->getGlobal('CookieVersion', 1)->value),
                ),
            ],
        );
    }

    /**
     * @return array<ConsentStatisticByDayGroupedByServiceGroupEntryModel>
     */
    public function getLastDaysGroupedByServiceGroup(int $days): array
    {
        $dateTime = (new DateTime())->setTime(0, 0)->sub(DateInterval::createFromDateString($days . ' days'));

        return $this->consentStatisticByDayGroupedByServiceGroupRepository->getAll(
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('date'),
                    '>=',
                    new LiteralExpression($dateTime->format('Y-m-d')),
                ),
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('cookieVersion'),
                    '=',
                    new LiteralExpression($this->option->getGlobal('CookieVersion', 1)->value),
                ),
            ],
        );
    }

    /**
     * @return array<ConsentStatisticByHourGroupedByServiceGroupEntryModel>
     */
    public function getTodayGroupedByServiceGroup(): array
    {
        $dateTime = (new DateTime())->setTime(0, 0);

        return $this->consentStatisticByHourGroupedByServiceGroupRepository->getAll(
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('date'),
                    '=',
                    new LiteralExpression($dateTime->format('Y-m-d')),
                ),
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('cookieVersion'),
                    '=',
                    new LiteralExpression($this->option->getGlobal('CookieVersion', 1)->value),
                ),
            ],
        );
    }
}
