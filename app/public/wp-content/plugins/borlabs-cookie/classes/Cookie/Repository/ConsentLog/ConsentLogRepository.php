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

namespace Borlabs\Cookie\Repository\ConsentLog;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\ConsentLog\ConsentLogModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\System\Config\GeneralConfig;

/**
 * @extends AbstractRepository<ConsentLogModel>
 */
final class ConsentLogRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = ConsentLogModel::class;

    public const TABLE = 'borlabs_cookie_consent_logs';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('consents', 'consents'),
            new PropertyMapItemDto('cookieVersion', 'cookie_version'),
            new PropertyMapItemDto('iabTcfTCString', 'iab_tcf_t_c_string'),
            new PropertyMapItemDto('isLatest', 'is_latest'),
            new PropertyMapItemDto('stamp', 'stamp'),
            new PropertyMapItemDto('uid', 'uid'),
        ]);
    }

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

    public function deleteAll(int $pastDays): void
    {
        $this->wpdb->query(
            $this->wpdb->prepare('
                DELETE FROM
                    `' . $this->getTablePrefix() . self::TABLE . '`
                WHERE
                    `stamp` < NOW() - INTERVAL %d DAY
               ', [
                $pastDays,
            ]),
        );

        $this->optimizeTable();
    }

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
     * @return array<ConsentLogModel>
     */
    public function getUidHistory(string $uid): array
    {
        return $this->find(
            [
                'uid' => $uid,
            ],
            [
                'id' => 'DESC',
            ],
        );
    }

    public function insertAsLatestConsent(ConsentLogModel $model): ConsentLogModel
    {
        /** @var ConsentLogModel[] $latestConsents */
        $latestConsents = $this->find(
            [
                'uid' => $model->uid,
                'isLatest' => true,
            ],
            [
                'id' => 'DESC',
            ],
        );

        if (
            isset($latestConsents[0])
            && $this->hasIdenticalConsents($model, $latestConsents[0])
            && $this->hasIdenticalIabTcfTcString($model, $latestConsents[0])
        ) {
            return $latestConsents[0];
        }

        // Replace current "isLatest" consent with $model as latest consent
        foreach ($latestConsents as $latestConsent) {
            $latestConsent->isLatest = false;
            $this->update($latestConsent);
        }

        $model->isLatest = true;

        return $this->insert($model);
    }

    protected function getDefaultTablePrefix(): string
    {
        return $this->generalConfig->get()->aggregateConsents ? $this->wpdb->base_prefix : $this->wpdb->prefix;
    }

    private function hasIdenticalConsents(ConsentLogModel $currentConsent, ?ConsentLogModel $latestConsent = null): bool
    {
        return isset($latestConsent) && $latestConsent->consents == $currentConsent->consents;
    }

    private function hasIdenticalIabTcfTcString(ConsentLogModel $currentConsent, ?ConsentLogModel $latestConsent = null): bool
    {
        return isset($latestConsent) && $latestConsent->iabTcfTCString == $currentConsent->iabTcfTCString;
    }
}
