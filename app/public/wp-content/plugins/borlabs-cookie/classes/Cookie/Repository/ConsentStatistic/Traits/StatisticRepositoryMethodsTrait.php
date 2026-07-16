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

namespace Borlabs\Cookie\Repository\ConsentStatistic\Traits;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Repository\Expression\AssignmentExpression;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\System\Config\GeneralConfig;

/**
 * @template T of AbstractModel
 */
trait StatisticRepositoryMethodsTrait
{
    protected Container $container;

    protected WpDb $wpdb;

    private GeneralConfig $generalConfig;

    public function __construct(
        Container $container,
        GeneralConfig $generalConfig,
        WpDb $wpdb
    ) {
        $this->container = $container;
        $this->generalConfig = $generalConfig;
        $this->wpdb = $wpdb;

        parent::__construct($this->container, $this->wpdb);
    }

    /**
     * @return array<T>
     */
    public function getAll(array $where = []): array
    {
        return $this->find($where, [
            'date' => 'DESC',
        ]);
    }

    /**
     * @param T $model
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return T
     */
    public function insertOrIncrementCount(AbstractModel $model, int $increment = 1): AbstractModel
    {
        return $this->insertOrUpdate(
            $model,
            [
                new AssignmentExpression(
                    new ModelFieldNameExpression('count'),
                    new BinaryOperatorExpression(
                        new ModelFieldNameExpression('count'),
                        '+',
                        new LiteralExpression($increment),
                    ),
                ),
            ],
        );
    }

    protected function getDefaultTablePrefix(): string
    {
        return $this->generalConfig->get()->aggregateConsents ? $this->wpdb->base_prefix : $this->wpdb->prefix;
    }
}
