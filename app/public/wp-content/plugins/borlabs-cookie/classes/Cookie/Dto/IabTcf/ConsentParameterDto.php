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

final class ConsentParameterDto extends AbstractDto
{
    public string $description;

    public int $id;

    /**
     * @var string[]
     */
    public array $illustrations;

    public string $name;

    /**
     * @param string[] $illustrations
     */
    public function __construct(int $id, string $name, string $description, array $illustrations)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->illustrations = $illustrations;
    }
}
