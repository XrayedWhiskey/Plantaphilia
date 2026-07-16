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

namespace Borlabs\Cookie\Controller\Admin\Dashboard;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\Dashboard\DashboardLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Dashboard\ChartDataService;
use Borlabs\Cookie\System\Dashboard\NewsService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\SystemCheck\SystemCheck;
use Borlabs\Cookie\System\Template\Template;

/**
 * Class DashboardController.
 */
final class DashboardController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie';

    private ChartDataService $chartDataService;

    private Language $language;

    private NewsService $newsService;

    private Option $option;

    private SystemCheck $systemCheck;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        ChartDataService $chartDataService,
        Language $language,
        NewsService $newsService,
        Option $option,
        SystemCheck $systemCheck,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->chartDataService = $chartDataService;
        $this->language = $language;
        $this->newsService = $newsService;
        $this->option = $option;
        $this->systemCheck = $systemCheck;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function route(RequestDto $request): ?string
    {
        return $this->viewOverview($request->postData);
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->postData['action'])
            && in_array($request->postData['action'], ['chart-data', ], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->postData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewOverview(array $postData): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = DashboardLocalizationStrings::get();
        $templateData['localized']['global'] = GlobalLocalizationStrings::get();
        $templateData['language'] = $this->language->getSelectedLanguageCode();
        $templateData['data']['news'] = $this->newsService->getNews();
        $timeRange = '30days';

        if (isset($postData['timeRange']) && $postData['timeRange'] === 'today') {
            $timeRange = 'today';
        } elseif (isset($postData['timeRange']) && $postData['timeRange'] === '7days') {
            $timeRange = '7days';
        } elseif (isset($postData['timeRange']) && $postData['timeRange'] === 'services30days') {
            $timeRange = 'services30days';
        }

        $chartData = $this->chartDataService->getChartData($timeRange);

        $templateData['data']['timeRange'] = $timeRange;
        $templateData['data']['jsonChartData'] = isset($chartData['datasets'][0]['data'][0]) ? true : false;
        $templateData['scriptTagChartData'] = '<script>var barChartData = ' . json_encode($chartData) . '; </script>';
        $templateData['localized']['headline']['cookieVersion'] = Formatter::interpolate(
            $templateData['localized']['headline']['cookieVersion'],
            [
                'cookieVersion' => $this->option->getGlobal('CookieVersion', 1)->value,
            ],
        );

        // Contains parsed template of system status section
        $templateData['template']['systemCheck'] = $this->systemCheck->systemCheckView();

        return $this->template->getEngine()->render(
            'dashboard/dashboard.html.twig',
            $templateData,
        );
    }
}
