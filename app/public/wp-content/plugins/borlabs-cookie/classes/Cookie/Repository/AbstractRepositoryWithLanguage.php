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

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\System\Language\Language;

/**
 * @template TModel of AbstractModel
 *
 * @extends AbstractRepository<TModel>
 */
abstract class AbstractRepositoryWithLanguage extends AbstractRepository
{
    protected Language $language;

    public function __construct(
        Container $container,
        WpDb $wpdb,
        Language $language
    ) {
        parent::__construct($container, $wpdb);
        $this->language = $language;
    }
}
