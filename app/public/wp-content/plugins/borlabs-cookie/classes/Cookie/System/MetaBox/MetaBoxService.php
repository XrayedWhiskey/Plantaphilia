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

namespace Borlabs\Cookie\System\MetaBox;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Adapter\WpPost;
use Borlabs\Cookie\Localization\MetaBox\MetaBoxEditLocalizationStrings;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Template\Template;

final class MetaBoxService
{
    /**
     * @var array<string>
     */
    private array $customCodes = [];

    private MetaBoxEditLocalizationStrings $metaBoxEditLocalizationStrings;

    private PluginConfig $pluginConfig;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        MetaBoxEditLocalizationStrings $metaBoxEditLocalizationStrings,
        PluginConfig $pluginConfig,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->metaBoxEditLocalizationStrings = $metaBoxEditLocalizationStrings;
        $this->pluginConfig = $pluginConfig;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function addMetaBoxIfPostTypeEnabled(): void
    {
        $currentScreenData = $this->wpFunction->getCurrentScreen();

        if (
            isset($currentScreenData->post_type, $this->pluginConfig->get()->metaBox[$currentScreenData->post_type])
        ) {
            $this->wpFunction->addMetaBox(
                'borlabs-cookie-meta-box',
                $this->metaBoxEditLocalizationStrings::get()['headline']['borlabsCookieMetaBox'],
                [$this, 'displayMetaBox'],
                'normal',
                'default',
            );
        }
    }

    public function displayMetaBox(object $post): void
    {
        $templateData = [];
        $templateData['localized'] = $this->metaBoxEditLocalizationStrings::get();
        $templateData['data']['customCode'] = $this->wpFunction->getPostMeta($post->ID, 'borlabsCookieCustomCode', true);

        echo $this->template->getEngine()->render(
            'meta-box/meta-box.html.twig',
            $templateData,
        );
    }

    public function init(): void
    {
        $this->wpFunction->addAction('the_post', [$this, 'queueCustomCode']);
        $this->wpFunction->addAction('wp_footer', [$this, 'outputCustomCode']);
    }

    /**
     * Outputs the custom codes and clears the customCodes array.
     */
    public function outputCustomCode(): void
    {
        if (!empty($this->customCodes)) {
            echo implode("\n", $this->customCodes);
            $this->customCodes = [];
        }
    }

    public function queueCustomCode(): void
    {
        $postType = WpPost::getInstance()->post_type ?? null;

        if ($postType === null) {
            return;
        }

        if (!isset($this->pluginConfig->get()->metaBox[$postType])) {
            return;
        }

        $customCode = $this->wpFunction->getPostMeta(WpPost::getInstance()->ID, 'borlabsCookieCustomCode', true);

        if (!empty($customCode)) {
            $this->customCodes[WpPost::getInstance()->ID] = $this->wpFunction->doShortcode($customCode);
        }
    }

    public function register(): void
    {
        $this->wpFunction->addAction('add_meta_boxes', [$this, 'addMetaBoxIfPostTypeEnabled']);
        $this->wpFunction->addAction('save_post', [$this, 'save'], 10, 3);
    }

    public function save(int $postId, ?object $post = null, ?bool $update = null): void
    {
        if (isset($_POST['borlabsCookie']['customCode'])) {
            $this->wpFunction->updatePostMeta(
                $postId,
                'borlabsCookieCustomCode',
                $_POST['borlabsCookie']['customCode'],
            );
        }
    }
}
