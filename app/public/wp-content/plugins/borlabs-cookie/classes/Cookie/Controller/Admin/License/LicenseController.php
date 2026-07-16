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

namespace Borlabs\Cookie\Controller\Admin\License;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\License\LicenseLocalizationStrings;
use Borlabs\Cookie\Localization\ValidatorLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\Validator\License\LicenseValidator;
use DateTime;

/**
 * Singleton class LicenseController.
 *
 * The **LicenseController** class takes care of displaying the license section in the backend.
 * It also processes all requests that can be executed in the license section.
 */
final class LicenseController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-license';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private License $license;

    private LicenseLocalizationStrings $licenseLocalizationStrings;

    private LicenseValidator $licenseValidator;

    private MessageManager $messageManager;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        License $license,
        LicenseLocalizationStrings $licenseLocalizationStrings,
        LicenseValidator $licenseValidator,
        MessageManager $messageManager,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->license = $license;
        $this->licenseLocalizationStrings = $licenseLocalizationStrings;
        $this->licenseValidator = $licenseValidator;
        $this->messageManager = $messageManager;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Updates information of the current license.
     */
    public function refresh(): string
    {
        if (!isset($this->license->get()->licenseKey)) {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['unknown'], ['message' => 'noLicenseKey']);

            return $this->viewOverview();
        }

        try {
            $this->license->register($this->license->get()->licenseKey);
            $this->messageManager->success($this->licenseLocalizationStrings::get()['alert']['licenseRefreshedSuccessfully']);
        } catch (LicenseApiClientException $e) {
            if (in_array($e->getMessage(), ['invalidLicenseKey', 'licenseBlockedDomain', 'licenseQuotaExceeded', ], true)) {
                $this->license->remove();
            }

            $this->messageManager->error($e->getTranslatedMessage());
        }

        return $this->viewOverview();
    }

    /**
     * Registers the license key for the current site.
     *
     * @param array<string> $postData only key 'licenseKey' is required by this method
     *
     * @throws \Borlabs\Cookie\Exception\ApiClient\IabTcfApiClientException
     */
    public function register(array $postData): string
    {
        if (!$this->licenseValidator->isValid($postData)) {
            return $this->viewOverview();
        }

        $this->license->register($postData['licenseKey']);
        $this->messageManager->success($this->licenseLocalizationStrings::get()['alert']['licenseRegisteredSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\IabTcfApiClientException
     */
    public function remove(): string
    {
        $this->license->remove();
        $this->messageManager->success($this->licenseLocalizationStrings::get()['alert']['licenseRemovedSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'refresh') {
                $this->refresh();
            }

            if ($action === 'register') {
                $this->register($request->postData);
            }

            if ($action === 'remove') {
                $this->remove();
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview();
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->postData['action'])
            && in_array($request->postData['action'], ['refresh', 'register', 'remove'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->postData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * Returns the overview.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $licenseData = $this->license->get();
        $licenseStatus = $this->license->isLicenseValid() ? 'valid' : 'expired';

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->licenseLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $licenseData;
        $templateData['data']['licenseKeyMasked'] = preg_replace('/[^\-]/', '*', $licenseData->licenseKey ?? '');
        $templateData['data']['licenseStatus'] = $licenseStatus;
        $templateData['data']['licenseStatusMessage'] = $templateData['localized']['text'][$licenseStatus];

        $templateData['data']['licenseValidUntil'] = '';
        $licenseValidUntil = !empty($licenseData->licenseValidUntil) ? new DateTime($licenseData->licenseValidUntil) : null;

        if (!empty($licenseValidUntil)) {
            $templateData['data']['licenseValidUntil'] = Formatter::timestamp($licenseValidUntil->getTimestamp(), null, '');
        }

        $validationLocalization = ValidatorLocalizationStrings::get();
        $templateData['localized']['validation']['licenseKey'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            ['fieldName' => $templateData['localized']['field']['licenseKey']],
        );

        return $this->template->getEngine()->render(
            'license/license.html.twig',
            $templateData,
        );
    }
}
