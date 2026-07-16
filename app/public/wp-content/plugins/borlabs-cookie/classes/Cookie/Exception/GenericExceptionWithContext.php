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

namespace Borlabs\Cookie\Exception;

use Throwable;

class GenericExceptionWithContext extends GenericException
{
    private array $context;

    public function __construct(
        string $message = '',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $previous);

        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
