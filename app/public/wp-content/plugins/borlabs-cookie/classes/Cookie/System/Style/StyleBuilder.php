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

namespace Borlabs\Cookie\System\Style;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\Config\AbstractConfigDto;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Support\Converter;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class StyleBuilder
{
    public const ANIMATE_CSS_SOURCE_FOLDER = 'assets/external/animate.css/source';

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private FileManager $fileManager;

    private Log $log;

    private Option $option;

    private WidgetConfig $widgetConfig;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        FileManager $fileManager,
        Log $log,
        Option $option,
        WidgetConfig $widgetConfig,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->fileManager = $fileManager;
        $this->log = $log;
        $this->option = $option;
        $this->widgetConfig = $widgetConfig;
        $this->wpFunction = $wpFunction;
    }

    public function applyCssModifications(string $css): string
    {
        return $this->wpFunction->applyFilter('borlabsCookie/styleBuilder/modifyCss', $css);
    }

    public function buildCssFile(int $blogId, string $languageCode): bool
    {
        $cssFileName = $this->getCssFileName($blogId, $languageCode);
        $css = $this->getDefaultCss();
        $css .= $this->getDialogCss($languageCode);
        $css .= $this->getWidgetVariableCss($languageCode);
        $css .= $this->getAnimationCss($languageCode);
        $css .= $this->getCustomCss($languageCode);
        $css .= $this->getContentBlockerCss($languageCode);
        $css = $this->applyCssModifications($css);

        if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_ASSET_TIMESTAMPS') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_ASSET_TIMESTAMPS') === true) {
            $css = '/* ' . date('Y-m-d H:i:s') . ' */ .brlbs-debug[id="timestamp-' . date('Y-m-d-H-i-s') . '"] { color: red; } ' . $css;
        }

        return $this->fileManager->cacheFile($cssFileName, $css) !== null;
    }

    public function getAnimationCss(string $languageCode): string
    {
        $css = '';
        $dialogSettingsConfig = $this->dialogSettingsConfig->load($languageCode);

        if ($dialogSettingsConfig->animation === false) {
            return $css;
        }

        // Animation in
        $animationIn = $this->findAnimateCssFilepath($dialogSettingsConfig->animationIn);

        if (file_exists($animationIn)) {
            $css .= $this->transformAnimationCss($dialogSettingsConfig->animationIn, $animationIn);
        }

        // Animation out
        $animationOut = $this->findAnimateCssFilepath($dialogSettingsConfig->animationOut);

        if (file_exists($animationOut)) {
            $css .= $this->transformAnimationCss($dialogSettingsConfig->animationOut, $animationOut);
        }

        // Change var
        return str_replace('--animate-duration', '--dialog-animation-duration', $css);
    }

    public function getContentBlockerCss(string $languageCode): string
    {
        $css = '';
        $contentBlockers = $this->contentBlockerRepository->getAllActiveOfLanguage($languageCode);

        /** @var \Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel $contentBlocker */
        foreach ($contentBlockers as $contentBlocker) {
            if ($contentBlocker->previewCss !== '') {
                $contentBlocker->previewCss = str_replace(
                    'CONTENT-BLOCKER-ID-PLACEHOLDER',
                    $contentBlocker->key,
                    $contentBlocker->previewCss,
                );
                $css .= $contentBlocker->previewCss;
            }
        }

        return $css . $this->getContentBlockerVariablesCss($languageCode);
    }

    public function getContentBlockerVariablesCss(string $languageCode)
    {
        $nonSpecificValues = [
            'backgroundColor',
            'buttonColor',
            'buttonColorHover',
            'buttonTextColor',
            'buttonTextColorHover',
            'fontFamily',
            'linkColor',
            'linkColorHover',
            'separatorColor',
            'textColor',
        ];

        $pixelValues = [
            'borderRadiusTopLeft',
            'borderRadiusTopRight',
            'borderRadiusBottomLeft',
            'borderRadiusBottomRight',
            'buttonBorderRadiusTopLeft',
            'buttonBorderRadiusTopRight',
            'buttonBorderRadiusBottomLeft',
            'buttonBorderRadiusBottomRight',
            'fontSize',
            'separatorWidth',
        ];

        $percentageToFloatValues = [
            'backgroundOpacity',
        ];

        $rgbaValues = [
            'background',
        ];

        return $this->generateCssVariables(
            $this->contentBlockerStyleConfig->load($languageCode),
            $nonSpecificValues,
            $pixelValues,
            $percentageToFloatValues,
            $rgbaValues,
            'content-blocker',
        );
    }

    public function getCssFileName(int $blogId, string $languageCode): string
    {
        return 'borlabs-cookie-' . $blogId . '-' . $languageCode . '.css';
    }

    public function getCustomCss(string $languageCode): string
    {
        return $this->dialogStyleConfig->load($languageCode)->customCss;
    }

    public function getDefaultCss(): string
    {
        $manifest = json_decode(file_get_contents(BORLABS_COOKIE_PLUGIN_PATH . '/assets/manifest.json', true), true);
        $cssContent = file_get_contents(BORLABS_COOKIE_PLUGIN_PATH . '/assets/' . $manifest['scss/frontend/borlabs-cookie.scss']['file']);

        if (is_string($cssContent)) {
            return $cssContent;
        }

        $this->log->error('Failed to load the default CSS file', [
            'stylesheetManifestEntry' => $manifest['scss/frontend/borlabs-cookie.scss'],
        ]);

        return '';
    }

    public function getDialogCss(string $languageCode): string
    {
        return $this->getDialogVariablesCss($languageCode);
    }

    public function getDialogVariablesCss(string $languageCode): string
    {
        $nonSpecificValues = [
            'dialogAnimationDelay',
            'dialogAnimationDuration',
            'dialogBackdropBackgroundColor',
            'dialogBackgroundColor',
            'dialogButtonAcceptAllColor',
            'dialogButtonAcceptAllColorHover',
            'dialogButtonAcceptAllTextColor',
            'dialogButtonAcceptAllTextColorHover',
            'dialogButtonAcceptOnlyEssentialColor',
            'dialogButtonAcceptOnlyEssentialColorHover',
            'dialogButtonAcceptOnlyEssentialTextColor',
            'dialogButtonAcceptOnlyEssentialTextColorHover',
            'dialogButtonCloseColor',
            'dialogButtonCloseColorHover',
            'dialogButtonCloseTextColor',
            'dialogButtonCloseTextColorHover',
            'dialogButtonPreferencesColor',
            'dialogButtonPreferencesColorHover',
            'dialogButtonPreferencesTextColor',
            'dialogButtonPreferencesTextColorHover',
            'dialogButtonSaveConsentColor',
            'dialogButtonSaveConsentColorHover',
            'dialogButtonSaveConsentTextColor',
            'dialogButtonSaveConsentTextColorHover',
            'dialogButtonSelectionColor',
            'dialogButtonSelectionColorHover',
            'dialogButtonSelectionTextColor',
            'dialogButtonSelectionTextColorHover',
            'dialogCheckboxBackgroundColorActive',
            'dialogCheckboxBackgroundColorDisabled',
            'dialogCheckboxBackgroundColorInactive',
            'dialogCheckboxBorderColorActive',
            'dialogCheckboxBorderColorDisabled',
            'dialogCheckboxBorderColorInactive',
            'dialogCheckboxCheckMarkColorActive',
            'dialogCheckboxCheckMarkColorDisabled',
            'dialogCardBackgroundColor',
            'dialogCardControlElementColor',
            'dialogCardControlElementColorHover',
            'dialogCardSeparatorColor',
            'dialogCardTextColor',
            'dialogControlElementColor',
            'dialogControlElementColorHover',
            'dialogFooterBackgroundColor',
            'dialogFooterTextColor',
            'dialogLinkPrimaryColor',
            'dialogLinkPrimaryColorHover',
            'dialogLinkSecondaryColor',
            'dialogLinkSecondaryColorHover',
            'dialogListItemBackgroundColorEven',
            'dialogListItemBackgroundColorOdd',
            'dialogListItemTextColorEven',
            'dialogListItemTextColorOdd',
            'dialogListItemControlElementColor',
            'dialogListItemControlElementColorHover',
            'dialogListItemControlElementSeparatorColor',
            'dialogListItemSeparatorColor',
            'dialogSearchBarInputBackgroundColor',
            'dialogSearchBarInputBorderColorDefault',
            'dialogSearchBarInputBorderColorFocus',
            'dialogSearchBarInputTextColor',
            'dialogSeparatorColor',
            'dialogSwitchButtonBackgroundColorActive',
            'dialogSwitchButtonBackgroundColorInactive',
            'dialogSwitchButtonColorActive',
            'dialogSwitchButtonColorInactive',
            'dialogTabBarTabBackgroundColorActive',
            'dialogTabBarTabBackgroundColorInactive',
            'dialogTabBarTabBorderColorBottomActive',
            'dialogTabBarTabBorderColorBottomInactive',
            'dialogTabBarTabBorderColorLeftActive',
            'dialogTabBarTabBorderColorLeftInactive',
            'dialogTabBarTabBorderColorRightActive',
            'dialogTabBarTabBorderColorRightInactive',
            'dialogTabBarTabBorderColorTopActive',
            'dialogTabBarTabBorderColorTopInactive',
            'dialogTabBarTabTextColorActive',
            'dialogTabBarTabTextColorInactive',
            'dialogTableRowBackgroundColorEven',
            'dialogTableRowBackgroundColorOdd',
            'dialogTableRowTextColorEven',
            'dialogTableRowTextColorOdd',
            'dialogTableRowBorderColor',
            'dialogTextColor',
            'dialogFontFamily',
        ];

        $pixelValues = [
            'dialogBorderRadiusBottomLeft',
            'dialogBorderRadiusBottomRight',
            'dialogBorderRadiusTopLeft',
            'dialogBorderRadiusTopRight',
            'dialogButtonBorderRadiusBottomLeft',
            'dialogButtonBorderRadiusBottomRight',
            'dialogButtonBorderRadiusTopLeft',
            'dialogButtonBorderRadiusTopRight',
            'dialogCardBorderRadiusBottomLeft',
            'dialogCardBorderRadiusBottomRight',
            'dialogCardBorderRadiusTopLeft',
            'dialogCardBorderRadiusTopRight',
            'dialogCardListPaddingMediumScreenBottom',
            'dialogCardListPaddingMediumScreenLeft',
            'dialogCardListPaddingMediumScreenRight',
            'dialogCardListPaddingMediumScreenTop',
            'dialogCardListPaddingSmallScreenBottom',
            'dialogCardListPaddingSmallScreenLeft',
            'dialogCardListPaddingSmallScreenRight',
            'dialogCardListPaddingSmallScreenTop',
            'dialogCheckboxBorderRadiusBottomLeft',
            'dialogCheckboxBorderRadiusBottomRight',
            'dialogCheckboxBorderRadiusTopLeft',
            'dialogCheckboxBorderRadiusTopRight',
            'dialogFontSize',
            'dialogListBorderRadiusBottomLeft',
            'dialogListBorderRadiusBottomRight',
            'dialogListBorderRadiusTopLeft',
            'dialogListBorderRadiusTopRight',
            'dialogListItemSeparatorWidth',
            'dialogListPaddingMediumScreenBottom',
            'dialogListPaddingMediumScreenLeft',
            'dialogListPaddingMediumScreenRight',
            'dialogListPaddingMediumScreenTop',
            'dialogListPaddingSmallScreenBottom',
            'dialogListPaddingSmallScreenLeft',
            'dialogListPaddingSmallScreenRight',
            'dialogListPaddingSmallScreenTop',
            'dialogSearchBarInputBorderRadiusBottomLeft',
            'dialogSearchBarInputBorderRadiusBottomRight',
            'dialogSearchBarInputBorderRadiusTopLeft',
            'dialogSearchBarInputBorderRadiusTopRight',
            'dialogSearchBarInputBorderWidthBottom',
            'dialogSearchBarInputBorderWidthLeft',
            'dialogSearchBarInputBorderWidthRight',
            'dialogSearchBarInputBorderWidthTop',
            'dialogTabBarTabBorderWidthBottomActive',
            'dialogTabBarTabBorderWidthLeftActive',
            'dialogTabBarTabBorderWidthRightActive',
            'dialogTabBarTabBorderWidthTopActive',
            'dialogTabBarTabBorderWidthBottomInactive',
            'dialogTabBarTabBorderWidthLeftInactive',
            'dialogTabBarTabBorderWidthRightInactive',
            'dialogTabBarTabBorderWidthTopInactive',
            'dialogTabBarTabBorderRadiusBottomLeftActive',
            'dialogTabBarTabBorderRadiusBottomRightActive',
            'dialogTabBarTabBorderRadiusTopLeftActive',
            'dialogTabBarTabBorderRadiusTopRightActive',
            'dialogTabBarTabBorderRadiusBottomLeftInactive',
            'dialogTabBarTabBorderRadiusBottomRightInactive',
            'dialogTabBarTabBorderRadiusTopLeftInactive',
            'dialogTabBarTabBorderRadiusTopRightInactive',
            'dialogTableBorderRadiusBottomLeft',
            'dialogTableBorderRadiusBottomRight',
            'dialogTableBorderRadiusTopLeft',
            'dialogTableBorderRadiusTopRight',
            'dialogTableCellPaddingBottom',
            'dialogTableCellPaddingLeft',
            'dialogTableCellPaddingRight',
            'dialogTableCellPaddingTop',
        ];

        $percentageToFloatValues = [
            'dialogBackdropBackgroundOpacity',
        ];

        return $this->generateCssVariables(
            $this->dialogStyleConfig->load($languageCode),
            $nonSpecificValues,
            $pixelValues,
            $percentageToFloatValues,
        );
    }

    public function getWidgetVariableCss(string $languageCode): string
    {
        return $this->generateCssVariables(
            $this->widgetConfig->load($languageCode),
            [
                'position',
                'color',
            ],
            [],
            [],
            [],
            'widget',
        );
    }

    public function updateCssFileAndIncrementStyleVersion(int $blogId, string $languageCode): bool
    {
        // Build CSS file
        if (!$this->buildCssFile($blogId, $languageCode)) {
            return false;
        }

        // Update the style version to bypass cached styles.
        $styleVersionOption = $this->option->get(
            'StyleVersion',
            1,
            $languageCode,
        );
        $this->option->set(
            'StyleVersion',
            (int) $styleVersionOption->value + 1,
            false,
            $languageCode,
        );

        return true;
    }

    private function findAnimateCssFilepath(string $animation): string
    {
        $filepath = '';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(BORLABS_COOKIE_PLUGIN_PATH . '/' . self::ANIMATE_CSS_SOURCE_FOLDER),
        );

        foreach ($iterator as $fileData) {
            if (basename($fileData->getPathname()) === $animation . '.css') {
                $filepath = $fileData->getPathname();

                break;
            }
        }

        return $filepath;
    }

    private function generateCssVariables(
        AbstractConfigDto $configDto,
        array $nonSpecificValues = [],
        array $pixelValues = [],
        array $percentageToFloatValues = [],
        array $rgbaValues = [],
        ?string $prefix = null
    ): string {
        $prefix = $prefix ? $prefix . '-' : '';
        $cssVariables = ':root{';

        foreach ($nonSpecificValues as $configKey) {
            $kebapCaseKey = Formatter::toKebabCase($configKey);
            $cssVariables .= '--' . $prefix . $kebapCaseKey . ': ' . $configDto->{$configKey} . ';' . PHP_EOL;
        }

        foreach ($pixelValues as $configKey) {
            $kebapCaseKey = Formatter::toKebabCase($configKey);
            $cssVariables .= '--' . $prefix . $kebapCaseKey . ': ' . $configDto->{$configKey} . 'px;' . PHP_EOL;
        }

        foreach ($percentageToFloatValues as $configKey) {
            $kebapCaseKey = Formatter::toKebabCase($configKey);
            $cssVariables .= '--' . $prefix . $kebapCaseKey . ': ' . $configDto->{$configKey} / 100 . ';' . PHP_EOL;
        }

        foreach ($rgbaValues as $configKey) {
            $rgb = Converter::hexToRgb($configDto->{$configKey . 'Color'});
            $kebapCaseKey = Formatter::toKebabCase($configKey);
            $cssVariables .= '--' . $prefix . $kebapCaseKey . ': rgba(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ' ,' . $configDto->{$configKey . 'Opacity'} / 100 . ');' . PHP_EOL;
        }

        $cssVariables .= '}' . PHP_EOL;

        return $cssVariables;
    }

    /**
     * Performs cleanups and css specificity transformations to make animate.css code
     * integrate with the borlabs cookie box.
     */
    private function transformAnimationCss(string $animationName, string $animationFilePath): string
    {
        // remove redundant .animated selector which exists on at least one animate.css animation (flip.css)
        $css = str_replace('.animated.', '.', file_get_contents($animationFilePath));

        // Animation classes need to be scoped in #BorlabsCookieBox because all: revert is
        // set on the element to prevent unwanted style overrides from the theme
        return str_replace('.' . $animationName, '#BorlabsCookieBox .' . $animationName, $css);
    }
}
