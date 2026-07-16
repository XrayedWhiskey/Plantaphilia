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

namespace Borlabs\Cookie\Controller\Admin\IabTcf;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\IabTcf\IabTcfConfigureVendorsLocalizationStrings;
use Borlabs\Cookie\Localization\IabTcf\IabTcfVendorDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\IabTcf\IabTcfVendorOverviewLocalizationStrings;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\IabTcf\VendorRepository;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\IabTcf\IabTcfService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class IabTcfVendorController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-iab-tcf-vendor';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private GeneralConfig $generalConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private IabTcfService $iabTcfService;

    private Language $language;

    private MessageManager $messageManager;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private VendorRepository $vendorRepository;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        GeneralConfig $generalConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        IabTcfService $iabTcfService,
        Language $language,
        MessageManager $messageManager,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        VendorRepository $vendorRepository,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->generalConfig = $generalConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->iabTcfService = $iabTcfService;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->vendorRepository = $vendorRepository;
        $this->wpFunction = $wpFunction;
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'configure') {
            return $this->viewConfigure();
        }

        // Switch status of IAB TCF vendor
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        if ($action === 'save') {
            return $this->save($request->postData);
        }

        return $this->viewOverview($request->postData, $request->getData);
    }

    public function save(array $postData): string
    {
        if (isset($postData['replaceVendorConfiguration']) && $postData['replaceVendorConfiguration'] === '1') {
            $this->vendorRepository->deactivateAll();
        }

        if (isset($postData['configuredVendors'])) {
            $configuredVendorIds = explode(',', $postData['configuredVendors']);
            $configuredVendorIds = array_filter(
                array_map('intval', $configuredVendorIds),
                fn ($value) => $value > 0,
            );

            foreach ($configuredVendorIds as $vendorId) {
                $vendor = $this->vendorRepository->getByVendorId($vendorId);

                if ($vendor !== null) {
                    $vendor->status = true;
                    $this->vendorRepository->update($vendor);
                }
            }
        }

        // TODO: do not use this, use language adapter to get list of configured languages
        $borlabsCookieConfigs = $this->generalConfig->getAllConfigs();

        foreach ($borlabsCookieConfigs as $optionData) {
            if ($optionData->language === $this->language->getSelectedLanguageCode()) {
                continue;
            }

            $this->iabTcfService->updateVendorConfiguration($optionData->language);
        }

        $this->iabTcfService->updateVendorConfiguration($this->language->getSelectedLanguageCode());
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return $this->viewOverview();
    }

    public function switchStatus(int $id): string
    {
        $this->vendorRepository->switchStatus($id);
        $this->iabTcfService->updateVendorConfiguration($this->language->getSelectedLanguageCode());
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->getData['action'])
            && in_array($request->getData['action'], ['configure',], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->getData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewConfigure(): string
    {
        if (!$this->iabTcfService->isGlobalVendorListDownloaded()) {
            $this->messageManager->warning(IabTcfConfigureVendorsLocalizationStrings::get()['alert']['globalVendorListNotDownloaded']);

            return $this->viewOverview();
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = IabTcfConfigureVendorsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        $translations = $this->iabTcfService->getTranslations($this->language->getSelectedLanguageCode());
        $templateData['purposes'] = $this->getCheckboxValuesList($translations->purposes->list);
        $templateData['features'] = $this->getCheckboxValuesList($translations->features->list);
        $templateData['specialFeatures'] = $this->getCheckboxValuesList($translations->specialFeatures->list);
        $templateData['specialPurposes'] = $this->getCheckboxValuesList($translations->specialPurposes->list);

        $this->borlabsCookieGlobalsService->addProperty('iabTcfTranslation', (array) $translations);
        $this->borlabsCookieGlobalsService->addProperty('iabTcfVendors', $this->iabTcfService->getVendorsFromVendorList());

        return $this->template->getEngine()->render(
            'iab-tcf/iab-tcf-vendor-manage/configure-iab-tcf-vendors.html.twig',
            $templateData,
        );
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $searchTerm = $postData['searchTerm'] ?? $getData['borlabs-search-term'] ?? null;
        $vendors = $this->vendorRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('name'),
                    'LIKE',
                    new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm ?? '')),
                ),
            ],
            ['status' => 'DESC', 'name' => 'ASC'],
            [],
            10,
            ['borlabs-search-term' => $searchTerm],
        );

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = IabTcfVendorOverviewLocalizationStrings::get();
        $templateData['localized']['vendorDetails'] = IabTcfVendorDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = [];
        $templateData['data']['vendors'] = $vendors;
        $templateData['data']['searchTerm'] = $searchTerm;

        $this->borlabsCookieGlobalsService->addProperty(
            'iabTcfTranslation',
            (array) $this->iabTcfService->getTranslations($this->language->getSelectedLanguageCode()),
        );

        return $this->template->getEngine()->render(
            'iab-tcf/iab-tcf-vendor-manage/overview-iab-tcf-vendor.html.twig',
            $templateData,
        );
    }

    private function getCheckboxValuesList(array $list): KeyValueDtoList
    {
        return new KeyValueDtoList(
            array_map(
                fn ($id) => new KeyValueDto((string) $id, (string) $id),
                array_keys(
                    array_fill(1, count($list), 1),
                ),
            ),
        );
    }
}
