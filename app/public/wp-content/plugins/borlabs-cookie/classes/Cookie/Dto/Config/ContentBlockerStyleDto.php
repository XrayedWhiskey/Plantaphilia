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
 * The **DialogStyleDto** class is used as a typed object that is passed within the system.
 *
 * The object contains styling properties to change the visual appearance of the dialog and other front-end parts of
 * Borlabs Cookie.
 *
 * @see \Borlabs\Cookie\System\Config\DialogStyleConfig
 */
final class ContentBlockerStyleDto extends AbstractConfigDto
{
    public string $backgroundColor = '#fafafa';

    public int $backgroundOpacity = 85;

    public int $borderRadiusBottomLeft = 4;

    public int $borderRadiusBottomRight = 4;

    public int $borderRadiusTopLeft = 4;

    public int $borderRadiusTopRight = 4;

    public int $buttonBorderRadiusBottomLeft = 4;

    public int $buttonBorderRadiusBottomRight = 4;

    public int $buttonBorderRadiusTopLeft = 4;

    public int $buttonBorderRadiusTopRight = 4;

    public string $buttonColor = '#0063e3';

    public string $buttonColorHover = '#1a66ff';

    public string $buttonTextColor = '#fff';

    public string $buttonTextColorHover = '#fff';

    public string $fontFamily = 'inherit';

    public bool $fontFamilyStatus = false;

    public int $fontSize = 14;

    public string $linkColor = '#2563eb';

    public string $linkColorHover = '#1e40af';

    public string $separatorColor = '#e5e5e5';

    public int $separatorWidth = 1;

    public string $textColor = '#555';

    public bool $useIndividualStyles = true;
}
