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

namespace Borlabs\Cookie\Repository\Log;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\Log\LogModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<LogModel>
 */
class LogRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = LogModel::class;

    public const TABLE = 'borlabs_cookie_logs';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('backtrace', 'backtrace'),
            new PropertyMapItemDto('context', 'context'),
            new PropertyMapItemDto('createdAt', 'created_at'),
            new PropertyMapItemDto('level', 'level'),
            new PropertyMapItemDto('message', 'message'),
            new PropertyMapItemDto('processId', 'process_id'),
        ]);
    }

    public function deleteAll(int $pastDays): void
    {
        $this->wpdb->query(
            $this->wpdb->prepare('
                DELETE FROM
                    `' . $this->getTablePrefix() . self::TABLE . '`
                WHERE
                    `created_at` < NOW() - INTERVAL %d DAY
               ', [
                $pastDays,
            ]),
        );

        $this->optimizeTable();
    }

    /**
     * @return array<LogModel>
     */
    public function getAll(): array
    {
        return $this->find(
            [],
            [
                'id' => 'DESC',
            ],
        );
    }

    /**
     * @return array<LogModel>
     */
    public function getProcessIdHistory(string $processId): array
    {
        return $this->find(
            [
                'processId' => $processId,
            ],
            [
                'id' => 'DESC',
            ],
        );
    }
}
