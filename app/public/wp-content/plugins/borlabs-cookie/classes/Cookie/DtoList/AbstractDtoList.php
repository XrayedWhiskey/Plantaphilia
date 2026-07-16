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

namespace Borlabs\Cookie\DtoList;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Exception\InvalidDtoException;
use Borlabs\Cookie\Support\Traits\ReflectionTrait;
use InvalidArgumentException;

/**
 * @template TDtoListType of \Borlabs\Cookie\Dto\AbstractDto
 *
 * @implements DtoListInterface<TDtoListType>
 */
abstract class AbstractDtoList extends AbstractDto implements DtoListInterface
{
    use ReflectionTrait;

    public const DTO_CLASS = '';

    public const UNIQUE_PROPERTY = '';

    /**
     * @var array<TDtoListType>
     */
    public array $list = [];

    /**
     * @param null|array<TDtoListType> $list
     */
    public function __construct(?array $list)
    {
        if ($list === null) {
            return;
        }

        foreach ($list as $dto) {
            $this->add($dto);
        }
    }

    /**
     * @param TDtoListType $dto
     */
    public function add(AbstractDto $dto, bool $prepend = false): bool
    {
        $this->validateDto($dto);

        foreach ($this->list as $dtoItem) {
            // If the UNIQUE_PROPERTY is not defined, we compare the object.
            if (static::UNIQUE_PROPERTY !== '' ? $dtoItem->{static::UNIQUE_PROPERTY} === $dto->{static::UNIQUE_PROPERTY} : $dto == $dtoItem) {
                return false;
            }
        }

        if ($prepend === false) {
            $this->list[] = $dto;
        } else {
            array_unshift($this->list, $dto);
        }

        return true;
    }

    /**
     * @param AbstractDtoList<TDtoListType> $dtoList
     */
    public function addList(AbstractDtoList $dtoList): void
    {
        foreach ($dtoList->list as $dtoItem) {
            $this->add($dtoItem);
        }
    }

    public function count(): int
    {
        return count($this->list);
    }

    /**
     * @param TDtoListType $dto
     *
     * @return null|TDtoListType
     */
    public function get(AbstractDto $dto): ?AbstractDto
    {
        $this->validateDto($dto);

        foreach ($this->list as $dtoItem) {
            if (static::UNIQUE_PROPERTY !== '' ? $dtoItem->{static::UNIQUE_PROPERTY} === $dto->{static::UNIQUE_PROPERTY} : $dto == $dtoItem) {
                return $dtoItem;
            }
        }

        return null;
    }

    /**
     * @param mixed $key
     *
     * @return null|TDtoListType
     */
    public function getByKey($key): ?AbstractDto
    {
        if (static::UNIQUE_PROPERTY === '') {
            return null;
        }

        foreach ($this->list as $dtoItem) {
            if ($dtoItem->{static::UNIQUE_PROPERTY} === $key) {
                return $dtoItem;
            }
        }

        return null;
    }

    /**
     * @param TDtoListType $dto
     */
    public function has(AbstractDto $dto): bool
    {
        return $this->get($dto) !== null;
    }

    public function isEmpty(): bool
    {
        return count($this->list) === 0;
    }

    /**
     * @param TDtoListType $dto
     */
    public function remove(AbstractDto $dto): bool
    {
        $this->validateDto($dto);

        foreach ($this->list as $index => $dtoItem) {
            if (static::UNIQUE_PROPERTY !== '' ? $dtoItem->{static::UNIQUE_PROPERTY} === $dto->{static::UNIQUE_PROPERTY} : $dto == $dtoItem) {
                array_splice($this->list, $index, 1);

                return true;
            }
        }

        return false;
    }

    public function sortListByPropertiesNaturally(array $properties): void
    {
        usort($this->list, function ($a, $b) use ($properties) {
            foreach ($properties as $property) {
                if (!property_exists($a, $property) || !property_exists($b, $property)) {
                    throw new InvalidArgumentException('The provided property does not exist in the objects.');
                }

                $comparison = strnatcmp(
                    (string) $a->{$property},
                    (string) $b->{$property},
                );

                if ($comparison !== 0) {
                    return $comparison;
                }
            }

            return 0;
        });
    }

    public function sortListByPropertyNaturally(string $property): void
    {
        usort($this->list, function ($a, $b) use ($property) {
            if (!property_exists($a, $property) || !property_exists($b, $property)) {
                throw new InvalidArgumentException('The provided property does not exist in the objects.');
            }

            return strnatcmp(
                (string) $a->{$property},
                (string) $b->{$property},
            );
        });
    }

    /**
     * @param TDtoListType $dto
     */
    public function update(AbstractDto $dto): bool
    {
        $this->validateDto($dto);

        foreach ($this->list as $index => $dtoItem) {
            if (static::UNIQUE_PROPERTY !== '' ? $dtoItem->{static::UNIQUE_PROPERTY} === $dto->{static::UNIQUE_PROPERTY} : $dto == $dtoItem) {
                $this->list[$index] = $dto;

                return true;
            }
        }

        return false;
    }

    /**
     * @param TDtoListType $dto
     */
    private function validateDto(AbstractDto $dto): void
    {
        if (static::DTO_CLASS === '') {
            throw new InvalidDtoException('Dto  ' . static::class . ' is missing the DTO_CLASS constant.');
        }

        if (get_class($dto) !== static::DTO_CLASS) {
            throw new InvalidDtoException('Dto is of type ' . get_class($dto) . ' but should be of type ' . static::DTO_CLASS . '.');
        }
    }
}
