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

namespace Borlabs\Cookie\System\Dashboard;

use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\ConsentStatistic\ConsentStatisticService;

class ChartDataService
{
    private ConsentStatisticService $consentStatisticService;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    public function __construct(
        ConsentStatisticService $consentStatisticService,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository
    ) {
        $this->consentStatisticService = $consentStatisticService;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function getChartData(string $timeRange): array
    {
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'labels' => '',
                    'backgroundColor' => [],
                    'borderColor' => [],
                    'borderWidth' => 1,
                    'data' => [],
                ],
            ],
        ];

        if ($timeRange === 'today') {
            $chartDataValues = $this->getChartDataToday();
        } elseif ($timeRange === '7days') {
            $chartDataValues = $this->getChartData7Days();
        } elseif ($timeRange === 'services30days') {
            // This is the only data NOT grouped by service-group-key
            $chartDataValues = $this->getChartDataServices30Days();
        } else {
            $chartDataValues = $this->getChartData30Days();
        }

        if (count($chartDataValues) === 0) {
            return $chartDataValues;
        }

        // Get all Service Groups
        $serviceGroups = $this->serviceGroupRepository->getAllOfSelectedLanguage();
        $services = $this->serviceRepository->getAllOfSelectedLanguage();
        $index = 0;

        if (!in_array($timeRange, ['30days', 'services30days'], true)) {
            $chartData['labels'] = array_keys($chartDataValues['essential']);

            $serviceGroupMap = array_column($serviceGroups, 'name', 'key');

            foreach ($chartDataValues as $serviceGroup => $data) {
                $chartData['datasets'][$index] = [
                    'borderColor' => $this->getColor($index, 1),
                    'data' => array_values($data),
                    'label' => $serviceGroupMap[$serviceGroup] ?? $serviceGroup,
                ];

                ++$index;
            }
        }

        if (in_array($timeRange, ['30days', 'services30days'], true)) {
            foreach (($timeRange === '30days' ? $serviceGroups : $services) as $data) {
                $chartData['labels'][] = $data->name;
                $chartData['datasets'][0]['backgroundColor'][$index] = $this->getColor($index, 0.8);
                $chartData['datasets'][0]['borderColor'][$index] = $this->getColor($index, 1);
                $chartData['datasets'][0]['data'][$index] = $chartDataValues[$data->key] ?? 0;
                ++$index;
            }
        }

        return $chartData;
    }

    private function getChartData30Days(): array
    {
        $chartDataValues = [];
        $dayEntries = $this->consentStatisticService->getLastDaysGroupedByServiceGroup(30);

        foreach ($dayEntries as $dayEntry) {
            if (!isset($chartDataValues[$dayEntry->serviceGroupKey])) {
                $chartDataValues[$dayEntry->serviceGroupKey] = 0;
            }

            $chartDataValues[$dayEntry->serviceGroupKey] += $dayEntry->count;
        }

        return $chartDataValues;
    }

    private function getChartData7Days(): array
    {
        $chartDataValues = [];
        $dayEntries = $this->consentStatisticService->getLastDaysGroupedByServiceGroup(7);

        foreach ($dayEntries as $dayEntry) {
            $dateKey = $dayEntry->date->format('Y-m-d');

            if (!isset($chartDataValues[$dayEntry->serviceGroupKey][$dateKey])) {
                $chartDataValues[$dayEntry->serviceGroupKey][$dateKey] = 0;
            }

            $chartDataValues[$dayEntry->serviceGroupKey][$dateKey] += $dayEntry->count;
        }

        return $chartDataValues;
    }

    private function getChartDataServices30Days(): array
    {
        $chartDataValues = [];
        $dayEntries = $this->consentStatisticService->getLastDays(30);

        foreach ($dayEntries as $dayEntry) {
            if (!isset($chartDataValues[$dayEntry->serviceKey])) {
                $chartDataValues[$dayEntry->serviceKey] = 0;
            }

            $chartDataValues[$dayEntry->serviceKey] += $dayEntry->count;
        }

        return $chartDataValues;
    }

    private function getChartDataToday(): array
    {
        $chartDataValues = [];
        $hourEntries = $this->consentStatisticService->getTodayGroupedByServiceGroup();

        foreach ($hourEntries as $hourEntry) {
            $hourKey = str_pad((string) $hourEntry->hour, 2, '0', STR_PAD_LEFT) . ':00';

            if (!isset($chartDataValues[$hourEntry->serviceGroupKey][$hourKey])) {
                $chartDataValues[$hourEntry->serviceGroupKey][$hourKey] = 0;
            }

            $chartDataValues[$hourEntry->serviceGroupKey][$hourKey] += $hourEntry->count;
        }

        return $chartDataValues;
    }

    private function getColor(int $index, float $opacity = 1.0): string
    {
        $colors = [
            'rgba(252, 165, 165, %opacity%)', // Red
            'rgba(253, 186, 116, %opacity%)', // Orange
            'rgba(190, 242, 100, %opacity%)', // Lime
            'rgba(103, 232, 249, %opacity%)', // Cyan
            'rgba(165, 180, 252, %opacity%)', // Indigo
            'rgba(249, 168, 212, %opacity%)', // Pink
            'rgba(252, 211, 77, %opacity%)', // Amber
            'rgba(134, 239, 172, %opacity%)', // Green
            'rgba(125, 211, 252, %opacity%)', // Sky
            'rgba(196, 181, 253, %opacity%)', // Violet
            'rgba(253, 164, 175, %opacity%)', // Rose
            'rgba(253, 224, 71, %opacity%)', // Yellow
            'rgba(110, 231, 183, %opacity%)', // Emerald
            'rgba(147, 197, 253, %opacity%)', // Blue
            'rgba(216, 180, 254, %opacity%)', // Purple
            'rgba(94, 234, 212, %opacity%)', // Teal
            'rgba(240, 171, 252, %opacity%)', // Fuchsia
        ];

        return str_replace(
            '%opacity%',
            (string) round($opacity, 2),
            $colors[$index] ?? $colors[0],
        );
    }
}
