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

namespace Borlabs\Cookie\Dto\Config;

/**
 * The **WidgetDto** class is used as a typed object that is passed within the system.
 *
 * The object contains content and behavior configuration properties related to the Borlabs Cookie Widget.
 *
 * @see \Borlabs\Cookie\System\Config\WidgetConfig
 */
final class WidgetDto extends AbstractConfigDto
{
    public string $color = '#555';

    public string $icon = 'borlabs-cookie-widget-a.svg';

    /**
     * @var string Default: `bottom-left`. The position of the borlabs cookie widget
     */
    public string $position = 'bottom-left';

    /**
     * @var bool default: `true`; `true`: The widget is displayed
     */
    public bool $show = true;
}
