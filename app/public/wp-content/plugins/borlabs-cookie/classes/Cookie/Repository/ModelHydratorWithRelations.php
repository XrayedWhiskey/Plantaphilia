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

namespace Borlabs\Cookie\Repository;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\HasOneRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Model\Factory;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use LogicException;

/**
 * This hydrator fulfills two tasks:
 * 1. Each row of the result set is transformed into a model object.
 * 2. The model is filled (via sub-queries) with relationships, if they were added via `addRelation` beforehand.
 */
class ModelHydratorWithRelations extends ArrayHydrator
{
    private RepositoryInterface $modelRepository;

    private PropertyMapDto $propertyMapDto;

    private array $with = [];

    public function __construct(
        RepositoryInterface $modelRepository
    ) {
        $this->modelRepository = $modelRepository;

        $this->propertyMapDto = $this->modelRepository::propertyMap();
    }

    public function addRelation($relationName, $callback): void
    {
        $this->with = array_merge(
            $this->with,
            [
                [
                    'relationName' => $relationName,
                    'subWith' => $callback,
                ],
            ],
        );
    }

    public function hydrate(array $result): array
    {
        $data = [];

        foreach ($result as $item) {
            $model = Factory::new($this->modelRepository, (object) $item);

            foreach ($this->with as $with) {
                $relationName = $with['relationName'];
                $subWith = $with['subWith'];
                $relationInfoProperties = $this->modelRepository->getRelationInfoProperties(
                    $this->propertyMapDto,
                );
                $relationInfo = $relationInfoProperties[$relationName];

                /** @var AbstractRepository $relatedRepository */
                $relatedRepository = $this->modelRepository->getRepositoryForFqn($relationInfo->repository);

                $whereData = [];

                if ($relationInfo instanceof HasManyRelationDto || $relationInfo instanceof BelongsToRelationDto) {
                    $whereData[$relationInfo->referencedJoinProperty] = $model->{$relationInfo->joinProperty};
                } elseif ($relationInfo instanceof HasOneRelationDto) {
                    $relatedPropertyMap = ($relationInfo->repository)::propertyMap();
                    $mappedRelationInfoProperties = $relatedRepository->getRelationInfoProperties($relatedPropertyMap)[$relationInfo->mappedBy];

                    if (!$mappedRelationInfoProperties instanceof BelongsToRelationDto) {
                        throw new LogicException('The mappedBy of an HasOneInverseRelationDto must be an HasOneRelationDto');
                    }
                    $whereData[$mappedRelationInfoProperties->joinProperty] = $model->id;
                } else {
                    throw new LogicException('Unexpected relation type');
                }

                $queryBuilder = $relatedRepository->getQueryBuilderWithRelations();

                foreach ($whereData as $fieldName => $value) {
                    if ($value !== null) {
                        $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression($fieldName), '=', new LiteralExpression($value)));
                    } else {
                        $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression($fieldName), 'IS', new NullExpression()));
                    }
                }

                if (is_callable($subWith)) {
                    $subWith($queryBuilder);
                }

                $query = $queryBuilder->getWpSelectQuery();

                if ($relationInfo instanceof HasManyRelationDto) {
                    $model->{$relationName} = $query->getResults();
                    /*
                     * DISABLED - Resolving the relation upwards makes it impossible to simply encode the object in JSON due to recursion.
                    foreach ($model->{$relationName} as $relatedModel) {
                        $relatedModel->{$relationInfo->mappedBy} = $model;
                    }*/
                } elseif ($relationInfo instanceof BelongsToRelationDto) {
                    $model->{$relationName} = $query->getResults()[0] ?? null;
                    /*
                     * DISABLED - Resolving the relation upwards makes it impossible to simply encode the object in JSON due to recursion.
                    if ($model->{$relationName} !== null && $relationInfo->inversedBy !== null) {
                        $relatedPropertyMap = ($relationInfo->repository)::propertyMap();
                        $mappedRelationInfoProperties = $relatedRepository->getRelationInfoProperties($relatedPropertyMap)[$relationInfo->inversedBy];

                        if ($mappedRelationInfoProperties instanceof HasManyRelationDto) {
                            $model->{$relationName}->{$relationInfo->inversedBy}[] = $model;
                        } elseif ($mappedRelationInfoProperties instanceof HasOneRelationDto) {
                            $model->{$relationName}->{$relationInfo->inversedBy} = $model;
                        } else {
                            throw new LogicException('Unexpected relation type ' . get_class($mappedRelationInfoProperties));
                        }
                    }*/
                } elseif ($relationInfo instanceof HasOneRelationDto) {
                    $model->{$relationName} = $query->getResults()[0] ?? null;
                    /*
                     * DISABLED - Resolving the relation upwards makes it impossible to simply encode the object in JSON due to recursion.
                    if ($model->{$relationName} !== null) {
                        $model->{$relationName}->{$relationInfo->mappedBy} = $model;
                    }
                    */
                } else {
                    throw new LogicException('Unexpected relation type ' . get_class($relationInfo));
                }
            }

            $data[] = $model;
        }

        return $data;
    }
}
