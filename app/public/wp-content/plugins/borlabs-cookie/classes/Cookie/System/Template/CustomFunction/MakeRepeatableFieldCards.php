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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\System\Template\RepeatableFieldCardGenerator;
use Borlabs\Cookie\System\Template\Template;

final class MakeRepeatableFieldCards
{
    private RepeatableFieldCardGenerator $repeatableFieldCardGenerator;

    private Template $template;

    public function __construct(RepeatableFieldCardGenerator $repeatableFieldCardGenerator, Template $template)
    {
        $this->repeatableFieldCardGenerator = $repeatableFieldCardGenerator;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction(
                'makeRepeatableFieldCards',
                function (
                    object $repeatableSettingsFieldsList,
                    ?string $idPrefix = null,
                    ?string $idName = null
                ) {
                    return $this->repeatableFieldCardGenerator->makeCards(
                        $repeatableSettingsFieldsList,
                        $idPrefix,
                        $idName,
                    );
                },
            ),
        );
    }
}
