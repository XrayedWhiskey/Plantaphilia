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

namespace Borlabs\Cookie\Model\Package;

use Borlabs\Cookie\Dto\Package\ComponentDto;
use Borlabs\Cookie\Dto\System\VersionNumberDto;
use Borlabs\Cookie\Dto\System\VersionNumberWithHotfixDto;
use Borlabs\Cookie\DtoList\Package\TranslationDtoList;
use Borlabs\Cookie\Enum\Package\PackageTypeEnum;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use DateTimeInterface;

final class PackageModel extends AbstractModel
{
    public bool $autoUpdateEnabled = false;

    public bool $autoUpdateOverwriteCode = true;

    public bool $autoUpdateOverwriteTranslation = true;

    public string $borlabsServicePackageKey;

    public string $borlabsServicePackageSuccessorKey = '';

    public VersionNumberDto $borlabsServicePackageVersion;

    public ?DateTimeInterface $borlabsServiceUpdatedAt;

    /**
     * @var array<CompatibilityPatchModel>
     */
    public array $compatibilityPatches = [];

    public ComponentDto $components;

    /**
     * @var array<ContentBlockerModel>
     */
    public array $contentBlockers = [];

    public ?DateTimeInterface $installedAt;

    public bool $isDeprecated = false;

    public bool $isFeatured = false;

    public string $name;

    /**
     * @var array<ProviderModel>
     */
    public array $providers = [];

    public VersionNumberWithHotfixDto $requiredBorlabsCookieVersion;

    /**
     * @var array<ScriptBlockerModel>
     */
    public array $scriptBlockers = [];

    /**
     * @var array<ServiceModel>
     */
    public array $services = [];

    /**
     * @var array<StyleBlockerModel>
     */
    public array $styleBlockers = [];

    public string $thumbnail = '';

    public TranslationDtoList $translations;

    public PackageTypeEnum $type;

    public ?DateTimeInterface $updatedAt;

    public VersionNumberDto $version;
}
