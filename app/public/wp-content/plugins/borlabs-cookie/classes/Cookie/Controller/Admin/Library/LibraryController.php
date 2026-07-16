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

namespace Borlabs\Cookie\Controller\Admin\Library;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\CloudScan\CloudScanDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Library\LibraryLocalizationStrings;
use Borlabs\Cookie\Localization\RestClient\RestClientLocalizationStrings;
use Borlabs\Cookie\Repository\CloudScan\CloudScanSuggestionRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Package\PackageAutoUpdateJobService;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Template;
use DateTime;

final class LibraryController implements ControllerInterface, ExtendedRouteValidationInterface
{
    use SettingsFieldListTrait;
    use VersionNumberTrait;

    public const CONTROLLER_ID = 'borlabs-cookie-library';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private CloudScanService $cloudScanService;

    private CloudScanSuggestionRepository $cloudScanSuggestionRepository;

    private ContentBlockerRepository $contentBlockerRepository;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private LibraryLocalizationStrings $libraryLocalizationStrings;

    private MessageManager $messageManager;

    private PackageAutoUpdateJobService $packageAutoUpdateJobService;

    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        CloudScanService $cloudScanService,
        CloudScanSuggestionRepository $cloudScanSuggestionRepository,
        ContentBlockerRepository $contentBlockerRepository,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        LibraryLocalizationStrings $libraryLocalizationStrings,
        MessageManager $messageManager,
        PackageAutoUpdateJobService $packageAutoUpdateJobService,
        PackageManager $packageManager,
        PackageRepository $packageRepository,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->cloudScanService = $cloudScanService;
        $this->cloudScanSuggestionRepository = $cloudScanSuggestionRepository;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->libraryLocalizationStrings = $libraryLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->packageAutoUpdateJobService = $packageAutoUpdateJobService;
        $this->packageManager = $packageManager;
        $this->packageRepository = $packageRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function goToDetails(RequestDto $request)
    {
        $redirectUrl = $this->wpFunction->getAdminUrl('admin.php?page=' . self::CONTROLLER_ID);
        $borlabsServicePackageKey = $request->getData['borlabs-service-package-key'] ?? null;

        if ($borlabsServicePackageKey === null) {
            $this->wpFunction->wpSafeRedirect($redirectUrl);

            exit;
        }

        // Check if package exists
        $package = $this->packageRepository->getByPackageKey($borlabsServicePackageKey);

        if ($package === null) {
            $this->wpFunction->wpSafeRedirect($redirectUrl);

            exit;
        }

        $this->wpFunction->wpSafeRedirect(
            $this->wpFunction->getAdminUrl('admin.php?page=' . self::CONTROLLER_ID . '&action=details&id=' . $package->id . '&_wpnonce=' . $this->wpFunction->wpCreateNonce(self::CONTROLLER_ID . '-' . $package->id . '-details')),
        );

        exit;
    }

    public function refresh(RequestDto $request): string
    {
        try {
            $this->packageManager->updatePackageList();
            $this->messageManager->success($this->libraryLocalizationStrings::get()['alert']['libraryRefreshedSuccessfully']);
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview($request);
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $cloudScanId = $request->postData['cloudScanId'] ?? $request->getData['cloud-scan-id'] ?? null;
        $suggestionId = $request->postData['suggestionId'] ?? $request->getData['suggestionId'] ?? null;

        if ($cloudScanId !== null) {
            $cloudScanId = (int) $cloudScanId;
        }

        if ($suggestionId !== null) {
            $suggestionId = (int) $suggestionId;
        }

        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'bulk-install') {
                return $this->viewBulkInstall($cloudScanId, $request);
            }

            if ($action === 'details') {
                return $this->viewDetails($id, $request, $suggestionId);
            }

            if ($action === 'go-to-details') {
                $this->goToDetails($request);
            }

            if ($action === 'refresh') {
                return $this->refresh($request);
            }

            if ($action === 'save') {
                return $this->save($id, $request);
            }

            if ($action === 'uninstall') {
                return $this->uninstall($request, $id);
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview($request);
    }

    public function save(int $id, RequestDto $request): string
    {
        $package = $this->packageRepository->findById($id);

        if ($package === null) {
            $this->messageManager->error(LibraryLocalizationStrings::get()['alert']['packageNotFound']);

            return $this->viewOverview($request);
        }

        $packageSaveSettingsStatus = $this->packageManager->saveSettings($package, $request->postData);

        if ($packageSaveSettingsStatus) {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        } else {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['savedUnsuccessfully']);
        }

        return $this->viewDetails($id, $request);
    }

    public function uninstall(RequestDto $request, int $id): string
    {
        $package = $this->packageRepository->findById($id);

        if ($package === null) {
            $this->messageManager->error($this->libraryLocalizationStrings->get()['alert']['packageNotFound']);

            return $this->viewOverview($request);
        }

        if ($package->installedAt === null) {
            $this->messageManager->error($this->libraryLocalizationStrings->get()['alert']['packageIsNotInstalled']);

            return $this->viewOverview($request);
        }

        $languages = $this->language->getLanguageList();
        $config = [
            'language' => [],
        ];

        foreach ($languages->list as $language) {
            $config['language'][$language->key] = '1';
        }

        try {
            $uninstallStatusEntries = $this->packageManager->uninstall($package, $config);
            $failed = false;

            foreach ($uninstallStatusEntries as $uninstallStatusEntry) {
                if ($uninstallStatusEntry->status->is(InstallationStatusEnum::fromValue(InstallationStatusEnum::FAILURE))) {
                    $failed = true;

                    if ($uninstallStatusEntry->failureMessage !== null) {
                        $this->messageManager->error($this->libraryLocalizationStrings->get()['alert']['uninstallFailedWithMessage'], [
                            'type' => $uninstallStatusEntry->componentType->getDescription(),
                            'name' => $uninstallStatusEntry->name,
                            'message' => $uninstallStatusEntry->failureMessage,
                        ]);
                    } else {
                        $this->messageManager->error($this->libraryLocalizationStrings->get()['alert']['uninstallFailed'], [
                            'type' => $uninstallStatusEntry->componentType->getDescription(),
                            'name' => $uninstallStatusEntry->name,
                        ]);
                    }
                }
            }

            if (!$failed) {
                $this->messageManager->success($this->libraryLocalizationStrings->get()['alert']['uninstallSuccess'], [
                    'name' => $package->name,
                ]);
            }

            return $this->viewOverview($request);
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview($request);
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->postData['action'])
            && in_array($request->postData['action'], ['refresh',], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->postData['action'], $nonce)
        ) {
            $isValid = true;
        }

        if (isset($request->getData['action'], $request->getData['cloud-scan-id'])
            && in_array($request->getData['action'], ['bulk-install'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->getData['cloud-scan-id'] . '-' . $request->getData['action'], $nonce)
        ) {
            $isValid = true;
        }

        if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_LIBRARY_GO_TO_DETAILS_ROUTE')
            && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_LIBRARY_GO_TO_DETAILS_ROUTE')
            && isset($request->getData['action'], $request->getData['borlabs-service-package-key'])
            && in_array($request->getData['action'], ['go-to-details'], true)
        ) {
            $isValid = true;
        }

        if (isset($request->getData['action'], $request->getData['id'])
            && in_array($request->getData['action'], ['uninstall'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->getData['id'] . '-' . $request->getData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewBulkInstall(int $cloudScanId, RequestDto $request): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = LibraryLocalizationStrings::get();
        $templateData['localized']['showScan'] = CloudScanDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['restClient'] = RestClientLocalizationStrings::get();
        $templateData['data']['cloudScanId'] = $cloudScanId;
        $this->borlabsCookieGlobalsService->addProperty('restClientLocalizationStrings', RestClientLocalizationStrings::get());
        $suggestedPackages = $this->cloudScanService->getNotInstalledSuggestedPackages($cloudScanId);
        $templateData['data']['packages'] = $suggestedPackages->list ?? [];

        $templateData['data']['componentTypes'] = array_column(ComponentTypeEnum::getAll(), 'description', 'value');
        $this->borlabsCookieGlobalsService->addProperty('componentTypes', array_column(ComponentTypeEnum::getAll(), 'description', 'value'));

        return $this->template->getEngine()->render('library/library-manage/bulk-install.html.twig', $templateData);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\EntryNotFoundException
     * @throws \Borlabs\Cookie\Exception\IncompatibleTypeException
     * @throws \Borlabs\Cookie\Exception\MissingRequiredArgumentException
     */
    public function viewDetails(int $id, RequestDto $request, ?int $suggestionId = null): string
    {
        // Check if package exists
        $package = $this->packageRepository->findById($id);

        if ($package === null) {
            $this->messageManager->error(LibraryLocalizationStrings::get()['alert']['packageNotFound']);

            return $this->viewOverview($request);
        }

        $searchTerm = trim((string) ($request->getData['borlabs-search-term'] ?? null));
        $filter = $request->getData['borlabs-filter'] ?? null;

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = LibraryLocalizationStrings::get();
        $templateData['localized']['showScan'] = CloudScanDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['restClient'] = RestClientLocalizationStrings::get();
        $this->borlabsCookieGlobalsService->addProperty('restClientLocalizationStrings', RestClientLocalizationStrings::get());

        $templateData['data']['additionalQueryParameters'] = http_build_query([
            'borlabs-filter' => $filter,
            'borlabs-search-term' => $searchTerm,
        ]);
        $templateData['data']['borlabsCookieVersion'] = $this->transformToVersionNumberWithHotfixDto(BORLABS_COOKIE_VERSION);
        $templateData['data']['componentTypes'] = array_column(ComponentTypeEnum::getAll(), 'description', 'value');
        $templateData['data']['job'] = $this->packageAutoUpdateJobService->getJob($package);
        $templateData['data']['languages'] = $this->language->getLanguageList();
        $templateData['data']['package'] = $package;

        foreach ($templateData['data']['package']->components->contentBlockers->list as &$componentData) {
            $this->mergeComponentSettingsWithCustomSettings($componentData, $this->contentBlockerRepository);
        }

        foreach ($templateData['data']['package']->components->services->list as &$componentData) {
            $this->mergeComponentSettingsWithCustomSettings($componentData, $this->serviceRepository, $this->serviceGroupRepository);
        }

        $templateData['data']['selectedLanguages'] = array_column($this->language->getLanguageList()->list, 'key', 'key');

        $this->borlabsCookieGlobalsService->addProperty('componentTypes', array_column(ComponentTypeEnum::getAll(), 'description', 'value'));

        if ($suggestionId !== null) {
            $templateData['data']['suggestion'] = $this->cloudScanSuggestionRepository->findById($suggestionId);
        } else {
            $templateData['data']['suggestion'] = null;
        }

        if ($package->isDeprecated && $package->borlabsServicePackageSuccessorKey !== '') {
            $successorPackage = $this->packageRepository->getByPackageKey($package->borlabsServicePackageSuccessorKey);

            $this->messageManager->error($this->libraryLocalizationStrings->get()['alert']['packageIsDeprecated']);

            if ($successorPackage !== null) {
                $this->messageManager->info($this->libraryLocalizationStrings->get()['alert']['successorPackageAvailable'], [
                    'link' => $this->wpFunction->wpNonceUrl(
                        '?page=borlabs-cookie-library&action=details&id=' . $successorPackage->id,
                        self::CONTROLLER_ID . '-' . $successorPackage->id . '-details',
                    ),
                    'name' => $successorPackage->name,
                ]);
            }
        }

        if ($this->compareVersionNumber($package->requiredBorlabsCookieVersion, $templateData['data']['borlabsCookieVersion'], '>')) {
            $templateData['localized']['alert']['requiredPluginVersion'] = Formatter::interpolate(
                $templateData['localized']['alert']['requiredPluginVersion'],
                [
                    'currentVersion' => $this->transformVersionNumberToComparableString($templateData['data']['borlabsCookieVersion']),
                    'version' => $this->transformVersionNumberToComparableString($package->requiredBorlabsCookieVersion),
                ],
            );
        }

        return $this->template->getEngine()->render('library/library-manage/details-package.html.twig', $templateData);
    }

    public function viewOverview(RequestDto $request): string
    {
        $searchTerm = trim((string) ($request->postData['searchTerm'] ?? $request->getData['borlabs-search-term'] ?? null));
        $filter = $request->postData['filter'] ?? $request->getData['borlabs-filter'] ?? null;
        $where = [];

        if (strlen($searchTerm) > 1) {
            $where[] = new BinaryOperatorExpression(
                new ModelFieldNameExpression('name'),
                'LIKE',
                new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
            );
        }

        if ($filter && ComponentTypeEnum::hasValue($filter)) {
            $where[] = new BinaryOperatorExpression(
                new ModelFieldNameExpression('type'),
                '=',
                new LiteralExpression($filter),
            );
        } elseif ($filter === 'installed-packages') {
            $where[] = new BinaryOperatorExpression(
                new ModelFieldNameExpression('installedAt'),
                'IS NOT',
                new NullExpression(),
            );
        }

        if ($filter !== 'installed-packages') {
            $where[] = new BinaryOperatorExpression(
                new ModelFieldNameExpression('isDeprecated'),
                '=',
                new LiteralExpression('0'),
            );
        }

        $packages = $this->packageRepository->paginate(
            (int) ($request->getData['borlabs-page'] ?? 1),
            $where,
            [
                'isFeatured' => 'DESC',
                'name' => 'ASC',
            ],
            [],
            24,
            [
                'borlabs-filter' => $filter,
                'borlabs-search-term' => $searchTerm,
            ],
        );

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = LibraryLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['isUpdateAvailable'] = count($this->packageRepository->getUpdatablePackages()) > 0;
        $templateData['data']['additionalQueryParameters'] = http_build_query([
            'borlabs-filter' => $filter,
            'borlabs-search-term' => $searchTerm,
        ]);
        $templateData['data']['componentTypes'] = array_column(ComponentTypeEnum::getAll(), 'description', 'value');
        $templateData['data']['filter'] = $filter;
        $templateData['data']['packages'] = $packages;
        $templateData['data']['packageListLastUpdate'] = $this->packageManager->getLastSuccessfulCheckWithApiTimestamp() ? (new DateTime())->setTimestamp($this->packageManager->getLastSuccessfulCheckWithApiTimestamp()) : null;
        $templateData['data']['searchTerm'] = $searchTerm;

        return $this->template->getEngine()->render('library/library-manage/overview-packages.html.twig', $templateData);
    }
}
