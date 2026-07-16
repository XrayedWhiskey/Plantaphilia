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

namespace Borlabs\Cookie\Dto\IabTcf;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\IabTcf\ConsentParameterDtoList;
use Borlabs\Cookie\DtoList\IabTcf\DataCategoryDtoList;

final class IabTcfTranslationDto extends AbstractDto
{
    public DataCategoryDtoList $dataCategories;

    public ConsentParameterDtoList $features;

    public string $language;

    public ConsentParameterDtoList $purposes;

    public ConsentParameterDtoList $specialFeatures;

    public ConsentParameterDtoList $specialPurposes;
}
