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
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Repository\Expression\AssignmentExpression;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use DateTimeInterface;
use LogicException;
use ReflectionProperty;

/**
 * This class provides another way of constructing a `RepositoryQuery`. This time not by passing in
 * `AbstractExpression` nodes, but instead by passing in a `AbstractModel`. From that `AbstractModel` clauses are inferred
 * as far as possible (what columns to select, what assignments to make, and a where clause to find the given model
 * in the database).
 *
 * TODO: Merge with `RepositoryQueryBuilderWithRelations` and split functionality into traits.
 */
class RepositoryModelQueryBuilder extends RepositoryQueryBuilder
{
    public function setModel(AbstractModel $model): void
    {
        if ($this->propertyExistsAndIsInitialized($model, 'id') && $model->id !== -1 && $model->id !== null) {
            $this->andWhere(new BinaryOperatorExpression(
                new ModelFieldNameExpression('id'),
                '=',
                new LiteralExpression($model->id),
            ));
        }

        foreach ($this->propertyMapDto->map as $propertyMapItem) {
            if ($propertyMapItem instanceof PropertyMapRelationItemDto) {
                if ($propertyMapItem->relationInfo instanceof BelongsToRelationDto) {
                    // Ignore this property because each model should have a dedicated property for the joinProperty column,
                    // and we decided that only the dedicated column should decide what is saved to the database.
                    // Given IDs wrapped in a different model (relation) are not relevant.
                } elseif ($propertyMapItem->relationInfo instanceof HasManyRelationDto) {
                    // Nothing to do in this case, as One2Many is not the owning side.
                } elseif ($propertyMapItem->relationInfo instanceof HasOneRelationDto) {
                    // Nothing to do in this case, as inverse side is not the owning side
                } else {
                    throw new LogicException('Unexpected relationInfo: ' . get_class($propertyMapItem->relationInfo));
                }
            } else {
                $this->addSelectColumn(new ModelFieldNameExpression($propertyMapItem->reference));

                if ($propertyMapItem->reference === 'id') {
                    if ($model->{$propertyMapItem->reference} === -1 || empty($model->{$propertyMapItem->reference})) {
                        continue;
                    }
                }

                if ($this->propertyExistsAndIsInitialized($model, $propertyMapItem->reference)) {
                    $this->addAssignment(
                        new AssignmentExpression(
                            new ModelFieldNameExpression($propertyMapItem->reference),
                            $this->prepareData($model->{$propertyMapItem->reference}),
                        ),
                    );
                }
            }
        }
    }

    private function prepareData($value)
    {
        if ($value === null) {
            return new NullExpression();
        }

        if ($value instanceof DateTimeInterface) {
            return new LiteralExpression($value->format('Y-m-d H:i:s'));
        }

        if ($value instanceof AbstractEnum) {
            return new LiteralExpression((string) $value);
        }

        if (is_array($value) || is_object($value)) {
            return new LiteralExpression(json_encode($value));
        }

        return new LiteralExpression($value);
    }

    /**
     * The specification says that properties of a model may not be initialized.
     * We cannot use `isset`, because it will return `false` for `null`, but `null` is a valid value for us.
     * We cannot use `property_exists` alone, because it will return `true` for all properties (but it may not be initialized).
     * Therefore, use the ReflectionAPI to make additionally sure that a property actually contains a value.
     *
     * @param mixed $object
     * @param mixed $property
     */
    private function propertyExistsAndIsInitialized($object, $property): bool
    {
        if (!property_exists($object, $property)) {
            return false;
        }

        $reflectionProperty = new ReflectionProperty(get_class($object), $property);

        return $reflectionProperty->isInitialized($object);
    }
}
