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

namespace Borlabs\Cookie\Repository\Job;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\DirectionAscExpression;
use Borlabs\Cookie\Repository\Expression\DirectionExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use Borlabs\Cookie\Repository\RepositoryInterface;
use DateTime;

/**
 * @extends AbstractRepository<JobModel>
 */
class JobRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = JobModel::class;

    public const TABLE = 'borlabs_cookie_jobs';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('createdAt', 'created_at'),
            new PropertyMapItemDto('executedAt', 'executed_at'),
            new PropertyMapItemDto('payload', 'payload'),
            new PropertyMapItemDto('plannedFor', 'planned_for'),
            new PropertyMapItemDto('type', 'type'),
        ]);
    }

    /**
     * If several jobs are found, the next planned job is returned.
     */
    public function findFirstPlannedJobByTypeAndPayload(string $type, ?array $payload = null): ?JobModel
    {
        $data = $this->findPlannedJobsByTypeAndPayload($type, $payload);

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }

    public function findPlannedJobsByTypeAndPayload(string $type, ?array $payload = null): ?array
    {
        $data = $this->find(
            [
                'executedAt' => null,
                'type' => $type,
                'payload' => $payload ? json_encode($payload) : null,
            ],
            [
                'plannedFor' => 'ASC',
            ],
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data;
    }

    public function getAllPlannedJobsOfType(string $type): array
    {
        return $this->find(
            [
                'executedAt' => null,
                'type' => $type,
            ],
            [
                'plannedFor' => 'ASC',
            ],
        );
    }

    public function getDueJobs(): array
    {
        $queryBuilder = $this->getQueryBuilderWithRelations();
        $queryBuilder->andWhere(new BinaryOperatorExpression(
            new ModelFieldNameExpression('plannedFor'),
            '<=',
            new LiteralExpression(
                (new DateTime('now'))->format('Y-m-d H:i:s'),
            ),
        ));
        $queryBuilder->andWhere(new BinaryOperatorExpression(
            new ModelFieldNameExpression('executedAt'),
            'IS',
            new NullExpression(),
        ));
        $queryBuilder->addOrderBy(
            new DirectionExpression(
                new ModelFieldNameExpression('plannedFor'),
                new DirectionAscExpression(),
            ),
        );

        return $queryBuilder->getWpSelectQuery()->getResults();
    }
}
