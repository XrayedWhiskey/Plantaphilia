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

namespace Borlabs\Cookie\System\Localization;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Localization\LocalizedClassDto;
use Borlabs\Cookie\DtoList\Localization\LocalizedClassDtoList;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;
use Borlabs\Cookie\Localization\LocalizationInterface;
use LogicException;
use SplFileInfo;

class LocalizedClassesService
{
    private Container $container;

    private ?LocalizedClassDtoList $localizationClasses = null;

    private ?LocalizedClassDtoList $localizedEnumClasses = null;

    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    public function getAllLocalizationClasses(): LocalizedClassDtoList
    {
        if ($this->localizationClasses !== null) {
            return $this->localizationClasses;
        }
        $this->localizationClasses = new LocalizedClassDtoList();

        $directory = BORLABS_COOKIE_PLUGIN_PATH . '/classes/Cookie/Localization/';
        $files = array_merge(
            glob($directory . '/*.php'),
            glob($directory . '/**/*.php'),
        );

        foreach ($files as $file) {
            if (substr($file, 0, strlen($directory)) !== $directory) {
                continue;
            }

            $classNameWithNamespace = $this->getClassNameWithNamespace($directory, 'Localization', $file);

            if ($classNameWithNamespace === LocalizationInterface::class) {
                continue;
            }

            if (!class_exists($classNameWithNamespace)) {
                throw new LogicException('Could not find class: ' . $classNameWithNamespace);
            }

            $object = $this->container->get($classNameWithNamespace);

            if (!$object instanceof LocalizationInterface) {
                throw new LogicException('Class does not implement ' . LocalizationInterface::class . ': ' . $classNameWithNamespace);
            }

            $this->localizationClasses->add(new LocalizedClassDto(
                $classNameWithNamespace,
                $object,
            ));
        }

        return $this->localizationClasses;
    }

    public function getAllLocalizedEnumClasses(): LocalizedClassDtoList
    {
        if ($this->localizedEnumClasses !== null) {
            return $this->localizedEnumClasses;
        }
        $this->localizedEnumClasses = new LocalizedClassDtoList();

        $directory = BORLABS_COOKIE_PLUGIN_PATH . '/classes/Cookie/Enum/';
        $files = array_merge(
            glob($directory . '/*.php'),
            glob($directory . '/**/*.php'),
        );

        foreach ($files as $file) {
            if (substr($file, 0, strlen($directory)) !== $directory) {
                continue;
            }

            $classNameWithNamespace = $this->getClassNameWithNamespace($directory, 'Enum', $file);

            if ($classNameWithNamespace === LocalizedEnumInterface::class) {
                continue;
            }

            if (!class_exists($classNameWithNamespace)) {
                throw new LogicException('Could not find class: ' . $classNameWithNamespace);
            }

            if (!in_array(LocalizedEnumInterface::class, class_implements($classNameWithNamespace), true)) {
                if (method_exists($classNameWithNamespace, 'localized')) {
                    throw new LogicException('Found enum with `localized` method which does not implement interface ' . LocalizedEnumInterface::class);
                }

                continue;
            }

            $this->localizedEnumClasses->add(new LocalizedClassDto($classNameWithNamespace));
        }

        return $this->localizedEnumClasses;
    }

    /**
     * @return class-string
     */
    private function getClassNameWithNamespace(string $directory, string $subdirectory, string $filename): string
    {
        $fileObject = new SplFileInfo($filename);

        $path = $fileObject->getPath();
        $subPath = trim(substr($path, strlen($directory)), '/');
        $subNamespace = str_replace('/', '\\', $subPath);

        $className = $fileObject->getBasename('.php');

        if (empty($subNamespace)) {
            return 'Borlabs\\Cookie\\' . $subdirectory . '\\' . $className;
        }

        return 'Borlabs\\Cookie\\' . $subdirectory . '\\' . $subNamespace . '\\' . $className;
    }
}
