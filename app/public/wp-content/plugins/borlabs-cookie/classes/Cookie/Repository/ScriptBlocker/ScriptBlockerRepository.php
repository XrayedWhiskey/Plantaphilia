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

namespace Borlabs\Cookie\Repository\ScriptBlocker;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<ScriptBlockerModel>
 */
final class ScriptBlockerRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = ScriptBlockerModel::class;

    public const TABLE = 'borlabs_cookie_script_blockers';

    protected const UNDELETABLE = true;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('handles', 'handles'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('onExist', 'on_exist'),
            new PropertyMapItemDto('phrases', 'phrases'),
            new PropertyMapItemDto('status', 'status'),
            new PropertyMapItemDto('undeletable', 'undeletable'),
        ]);
    }

    public function getAll(): array
    {
        return $this->find([], [
            'name' => 'ASC',
        ]);
    }

    public function getAllActive(): array
    {
        return $this->find(['status' => true]);
    }

    public function getByKey(string $key): ?ScriptBlockerModel
    {
        $data = $this->find(
            [
                'key' => $key,
            ],
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }

    public function switchStatus(int $id): void
    {
        $model = $this->findByIdOrFail($id);
        $model->status = !$model->status;
        $this->update($model);
    }
}
