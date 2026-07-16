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

namespace Borlabs\Cookie\Container;

use LogicException;

/**
 * Class Container.
 *
 * The **Container** class implements PSR-11.
 *
 * @see \Borlabs\Cookie\Container\AbstractConfigManagerWithLanguage::get
 * @see \Borlabs\Cookie\Container\AbstractConfigManagerWithLanguage::has
 * @see \Borlabs\Cookie\Container\AbstractConfigManagerWithLanguage::add
 * @see \Borlabs\Cookie\Container\AbstractConfigManagerWithLanguage::extend
 */
final class Container implements ContainerInterface
{
    /**
     * Stack of instantiated objects (specified via ID) used to detect circular dependencies.
     *
     * @var array<string>
     */
    private array $debugResolveChain = [];

    /**
     * @var array<string, ContainerService>
     */
    private array $services = [];

    public function __construct()
    {
        $this->add(self::class, $this);
    }

    /**
     * Add a new service to the container.
     *
     * @param string $id       The id of the service to create. Can be a fully qualified class name.
     * @param mixed  $concrete optional; Default: `null`; The object to return for the given id
     */
    public function add(string $id, $concrete = null): ContainerService
    {
        $containerService = new ContainerService($id, $concrete);
        $containerService->setContainer($this);
        $this->services[$id] = $containerService;

        return $this->services[$id];
    }

    public function clear(): void
    {
        $this->services = [];
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Container\ContainerService} for the given id so that it can be
     * extended.
     *
     * @param string $id The id of the service to modify. Can be a fully qualified class name.
     */
    public function extend(string $id): ContainerService
    {
        if (!$this->has($id) && class_exists($id)) {
            $this->add($id);
        }

        return $this->services[$id];
    }

    /**
     * Returns the concrete for the given id.
     *
     * @template TInstance
     *
     * @param class-string<TInstance> $id The id of the service to obtain. Can be a fully qualified class name.
     *
     * @return TInstance
     */
    public function get(string $id)
    {
        if (!$this->has($id) && class_exists($id)) {
            $this->add($id);
        }

        if (!array_key_exists($id, $this->services)) {
            if (count($this->debugResolveChain) > 0) {
                $caller = $this->debugResolveChain[count($this->debugResolveChain) - 1];

                throw new LogicException('Unknown ContainerService with id `' . $id . '` requested by ContainerService with id `' . $caller . '`');
            }

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $caller = $backtrace[0];

            throw new LogicException('Unknown ContainerService with id `' . $id . '` requested in `' . $caller['file'] . ':' . $caller['line'] . '`');
        }

        if (in_array($id, $this->debugResolveChain, true)) {
            $caller = $this->debugResolveChain[count($this->debugResolveChain) - 1];

            throw new LogicException('Circular dependency detected (`' . $id . '` <-> `' . $caller . '`): ' . print_r($this->debugResolveChain, true));
        }

        $this->debugResolveChain[] = $id;
        $return = $this->services[$id]->resolve();
        array_pop($this->debugResolveChain);

        return $return;
    }

    /**
     * Checks if a given id is available in the container.
     *
     * @param string $id The id of the service to check for. Can be a fully qualified class name.
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * Removes one id from the container.
     */
    public function remove(string $id)
    {
        unset($this->services[$id]);
    }
}
