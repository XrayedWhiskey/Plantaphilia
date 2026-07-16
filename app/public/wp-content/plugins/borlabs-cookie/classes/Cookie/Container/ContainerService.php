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

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;

final class ContainerService
{
    /**
     * Array of arguments for the container service.
     *
     * @var array<string, mixed>
     */
    private array $args = [];

    /**
     * The concrete of the container service. This can be a class name or an object right now.
     *
     * @var mixed
     */
    private $concrete;

    /**
     * A reference to the owning Container of the container service.
     */
    private ?Container $container;

    /**
     * The resolved instance.
     *
     * @var null|mixed
     */
    private $resolved;

    /**
     * This controls whether the old instance should be returned on subsequent
     * {@see Container::get} calls or a new instance.
     */
    private bool $shared = true;

    /**
     * Stack of instantiated objects used to detect circular dependencies.
     *
     * @param string $id       The id of the service to create. Can be a fully qualified class name.
     * @param mixed  $concrete optional; Default: `null`; The object to return for the given id
     */
    public function __construct(string $id, $concrete = null)
    {
        $this->concrete = $concrete ?? $id;
    }

    /**
     * Add a parameter for instantiation of the class.
     *
     * @param string $argName the name of the argument to set
     * @param mixed  $value   the value to set the argument to
     */
    public function addParameter(string $argName, $value): self
    {
        $this->args[$argName] = $value;

        return $this;
    }

    /**
     * This resolves the current container service. Called by {@see Container::get}.
     *
     * @return mixed
     */
    public function resolve()
    {
        if ($this->shared && $this->resolved !== null) {
            return $this->resolved;
        }

        if (is_string($this->concrete) && class_exists($this->concrete)) {
            $this->resolved = $this->resolveClass($this->concrete);
        }

        if (is_object($this->concrete)) {
            $this->resolved = $this->concrete;
        }

        if ($this->resolved === null) {
            throw new Exception($this->concrete . ' could not be resolved');
        }

        return $this->resolved;
    }

    /**
     * Set the owning container. Is called by {@see Container::add}.
     *
     * @param Container $container the owning container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * Modifies the shared property of a container service, i.e. if a new or the old
     * instance should be returned on subsequent calls for the id.
     *
     * @param bool $shared the value for `$shared`
     */
    public function setShared(bool $shared): self
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @param array<mixed> $arguments
     *
     * @throws ReflectionException
     *
     * @return array<mixed>
     */
    private function reflectArguments(ReflectionFunctionAbstract $method, array $arguments): array
    {
        $arguments = array_map(
            static function (ReflectionParameter $param) use ($arguments) {
                $argName = $param->getName();
                $argType = $param->getType();

                if (array_key_exists($argName, $arguments)) {
                    return new RawArgument($arguments[$argName]);
                }

                if ($param->isDefaultValueAvailable()) {
                    return new RawArgument($param->getDefaultValue());
                }

                if ($argType instanceof ReflectionNamedType) {
                    return new ClassName($argType->getName());
                }

                throw new Exception('Unable to reflect ' . $argName);
            },
            $method->getParameters(),
        );

        return $this->resolveArguments($arguments);
    }

    /**
     * @param array<mixed> $arguments
     *
     * @throws Exception
     *
     * @return array<mixed>
     */
    private function resolveArguments(array $arguments): array
    {
        return array_map(
            function ($arg) {
                if ($arg instanceof ClassName) {
                    $id = $arg->getValue();
                } elseif ($arg instanceof RawArgument) {
                    return $arg->getValue();
                } else {
                    return $arg;
                }

                return $this->container->get($id);
            },
            $arguments,
        );
    }

    private function resolveClass(string $class): object
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            $object = new $class();
        } else {
            $object = $reflectionClass->newInstanceArgs($this->reflectArguments($constructor, $this->args));
        }

        return $object;
    }
}
