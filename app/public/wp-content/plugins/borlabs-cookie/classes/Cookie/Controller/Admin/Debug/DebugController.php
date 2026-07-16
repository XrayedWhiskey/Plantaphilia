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

namespace Borlabs\Cookie\Controller\Admin\Debug;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Template\Template;

final class DebugController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-debug';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Template $template;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        Template $template
    ) {
        $this->template = $template;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
    }

    public function route(RequestDto $request): ?string
    {
        return $this->viewOverview();
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['options']['demoValues'] = new KeyValueDtoList([
            new KeyValueDto('value-a', 'Value A'),
            new KeyValueDto('value-b', 'Value B'),
            new KeyValueDto('value-c', 'Value C'),
        ]);
        $templateData['options']['demoValuesB'] = new KeyValueDtoList([
            new KeyValueDto('value-d', 'Value D'),
            new KeyValueDto('value-e', 'Value E'),
            new KeyValueDto('value-f', 'Value F'),
        ]);

        $templateData['data']['demoValues'] = ['value-b' => '1',];
        $templateData['data']['codeareaCss'] = "#myId { background: blue; }\n.my-class { color: red; }";
        $templateData['data']['codeareaHtml'] = '<p style="font-weight: bold;">Hello World!</p>';
        $templateData['data']['codeareaJavaScript'] = 'console.log(\'Hello world!\');';
        $templateData['data']['colorDuoSelection']['backgroundColor'] = '#2563eb';
        $templateData['data']['colorDuoSelection']['textColor'] = '#eeee22';
        $templateData['data']['colorSelection'] = '#8224e3';
        $templateData['data']['input'] = 'Hello world!';
        $templateData['data']['inputGroup'] = 123;
        $templateData['data']['inputOptional'] = 'Hello world!';
        $templateData['data']['language'] = 'en';
        $templateData['data']['orderableList'] = ['value-b', 'value-c', 'value-a',];
        $templateData['data']['text'] = 'Hello world!';
        $templateData['data']['textarea'] = "Hello world!\nHello world!\nHello world!\n";

        return $this->template->getEngine()->render(
            'debug/debug-form-components.html.twig',
            $templateData,
        );
    }
}
