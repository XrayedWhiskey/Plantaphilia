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

namespace Borlabs\Cookie\System\Config\Traits;

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use DateTime;
use InvalidArgumentException;

trait PackageAutoUpdateTimeHelperTrait
{
    private function getRandomDateTimeBetweenIgnoringSeconds(DateTime $from, DateTime $to): DateTime
    {
        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        if ($fromTimestamp > $toTimestamp) {
            throw new InvalidArgumentException('The `from` date must be earlier than the `to` date.');
        }

        $randomTimestamp = random_int(
            (int) floor($fromTimestamp / 60),
            (int) floor($toTimestamp / 60),
        ) * 60;

        return (new DateTime())->setTimestamp($randomTimestamp);
    }

    private function getRandomTimeWithinSpanIgnoringSeconds(string $timeSpan): DateTime
    {
        if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):00-(0[0-9]|1[0-9]|2[0-3]):59$/', $timeSpan)) {
            throw new InvalidArgumentException('Invalid time span format. It must be in the format H:00-H:59 and within valid hours.');
        }

        $fromDateTime = new DateTime('tomorrow');
        $fromDateTime->setTime(
            (int) preg_replace(
                '/([0-9]{2}):00-([0-9]{2}):59/',
                '$1',
                $timeSpan,
            ),
            0,
        );
        $toDateTime = new DateTime('tomorrow');
        $toDateTime->setTime(
            (int) preg_replace(
                '/([0-9]{2}):00-([0-9]{2}):59/',
                '$2',
                $timeSpan,
            ),
            59,
        );

        return $this->getRandomDateTimeBetweenIgnoringSeconds($fromDateTime, $toDateTime);
    }

    private function getTimeSpanFromTime(DateTime $time): string
    {
        $timeSpanList = $this->getTimeSpanList();

        foreach ($timeSpanList->list as $timeSpan) {
            [$startTime, $endTime] = explode('-', $timeSpan->key);

            if (
                strtotime($time->format('H:i')) >= strtotime($startTime)
                && strtotime($time->format('H:i')) <= strtotime($endTime)
            ) {
                return $timeSpan->key;
            }
        }

        return '08:30';
    }

    private function getTimeSpanList(): KeyValueDtoList
    {
        return new KeyValueDtoList(
            array_map(
                function ($hour) {
                    $time = sprintf('%02d:00-%02d:59', 2 * $hour, (2 * $hour) + 1);

                    return new KeyValueDto($time, $time);
                },
                array_keys(array_fill(0, 12, 1)),
            ),
        );
    }
}
