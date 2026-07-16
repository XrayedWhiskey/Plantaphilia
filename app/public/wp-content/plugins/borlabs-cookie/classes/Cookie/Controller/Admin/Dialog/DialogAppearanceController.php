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

namespace Borlabs\Cookie\Controller\Admin\Dialog;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\Config\DialogStyleDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\Dialog\DialogAppearanceLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\Validator\Dialog\DialogAppearanceValidator;

final class DialogAppearanceController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-dialog-appearance';

    private DialogStyleConfig $dialogStyleConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        DialogStyleConfig $dialogStyleConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): bool
    {
        // Get default settings
        $defaultConfig = $this->dialogStyleConfig->defaultConfig();
        // Save settings
        $this->dialogStyleConfig->save($defaultConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        } elseif ($action === 'save') {
            $validator = new DialogAppearanceValidator($this->messageManager);

            if ($validator->isValid($request->postData)) {
                $this->save($request->postData);
            }
        }

        return $this->viewOverview();
    }

    public function save(array $postData): bool
    {
        $styleConfig = $this->dialogStyleConfig->get();
        $styleConfig->dialogAnimationDelay = $this->dialogStyleConfig->defaultConfig()->dialogAnimationDelay;
        $styleConfig->dialogFontFamily = (string) ($postData['dialogFontFamily'] ?? 'inherit');
        $styleConfig->dialogFontFamilyStatus = (bool) ($postData['dialogFontFamilyStatus'] ?? false);

        /**
         * @var DialogStyleDto $styleConfig
         */
        $styleConfig = $this->dialogStyleConfig->mapPostDataToProperties($postData, $styleConfig);

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->dialogStyleConfig->save($styleConfig, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->dialogStyleConfig->save($styleConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = DialogAppearanceLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = $this->dialogStyleConfig->get();
        $templateData['languages'] = $this->language->getLanguageList();

        return $this->template->getEngine()->render(
            'dialog/dialog-appearance/dialog-appearance.html.twig',
            $templateData,
        );
    }
}
