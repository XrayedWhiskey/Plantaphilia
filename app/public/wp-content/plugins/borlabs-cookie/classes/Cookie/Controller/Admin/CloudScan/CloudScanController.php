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

namespace Borlabs\Cookie\Controller\Admin\CloudScan;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageTypeEnum;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\CloudScan\CloudScanApiLocalizationStrings;
use Borlabs\Cookie\Localization\CloudScan\CloudScanCreateLocalizationStrings;
use Borlabs\Cookie\Localization\CloudScan\CloudScanDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\CloudScan\CloudScanOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Library\LibraryLocalizationStrings;
use Borlabs\Cookie\Localization\RestClient\RestClientLocalizationStrings;
use Borlabs\Cookie\Repository\CloudScan\CloudScanRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanSuggestionRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\DirectionAscExpression;
use Borlabs\Cookie\Repository\Expression\DirectionExpression;
use Borlabs\Cookie\Repository\Expression\ListExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\RepositoryQueryBuilderWithRelations;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\Validator\CloudScan\CloudScanStoreValidator;

final class CloudScanController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-cloud-scan';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private CloudScanApiLocalizationStrings $cloudScanApiLocalizationStrings;

    private CloudScanRepository $cloudScanRepository;

    private CloudScanService $cloudScanService;

    private CloudScanStoreValidator $cloudScanStoreValidator;

    private CloudScanSuggestionRepository $cloudScanSuggestionRepository;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private MessageManager $messageManager;

    private PackageRepository $packageRepository;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        CloudScanApiLocalizationStrings $cloudScanApiLocalizationStrings,
        CloudScanRepository $cloudScanRepository,
        CloudScanService $cloudScanService,
        CloudScanStoreValidator $cloudScanStoreValidator,
        CloudScanSuggestionRepository $cloudScanSuggestionRepository,
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        PackageRepository $packageRepository,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->cloudScanApiLocalizationStrings = $cloudScanApiLocalizationStrings;
        $this->cloudScanRepository = $cloudScanRepository;
        $this->cloudScanService = $cloudScanService;
        $this->cloudScanStoreValidator = $cloudScanStoreValidator;
        $this->cloudScanSuggestionRepository = $cloudScanSuggestionRepository;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->packageRepository = $packageRepository;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            // Create Scan
            if ($action === 'create') {
                return $this->viewCreate($request->postData);
            }

            // Store Scanner
            if ($action === 'store') {
                return $this->store($request->postData);
            }

            // View Scan Result Details
            if ($action === 'details') {
                return $this->viewDetails($id);
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview($request->postData, $request->getData);
    }

    public function store(array $postData): ?string
    {
        if (!$this->cloudScanStoreValidator->isValid($postData)) {
            return $this->viewCreate($postData);
        }

        try {
            $urls = $this->cloudScanService->getListOfPagesByType(
                $postData['selectPageType'],
                Sanitizer::booleanString($postData['enableCustomScanUrls'] ?? ''),
                $postData['scanPageUrl'] ?? null,
                $postData['customScanUrls'] ?? null,
            );

            $cloudScanModel = $this->cloudScanService->createScan(
                $urls,
                CloudScanTypeEnum::fromValue($postData['selectScanType']),
                isset($postData['enableHttpAuth']) ? ($postData['httpAuthUsername'] ?? null) : null,
                isset($postData['enableHttpAuth']) ? ($postData['httpAuthPassword'] ?? null) : null,
            );

            return $this->viewDetails($cloudScanModel->id);
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewCreate($postData);
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (
            isset($request->getData['action'], $request->getData['id'])
            && in_array($request->getData['action'], ['create',], true)
            && $this->wpFunction->wpVerifyNonce(
                self::CONTROLLER_ID . '-' . $request->getData['id'] . '-' . $request->getData['action'],
                $nonce,
            )
        ) {
            $isValid = true;
        } else {
            if (
                isset($request->getData['action']) && in_array($request->getData['action'], ['create', 'store'], true)
                && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->getData['action'], $nonce)
            ) {
                $isValid = true;
            }
        }

        return $isValid;
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewCreate(array $postData): ?string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = CloudScanCreateLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['pageTypes'] = PageTypeEnum::getLocalizedKeyValueList();
        $templateData['scanTypes'] = CloudScanTypeEnum::getLocalizedKeyValueList();
        $pages = $this->wpFunction->getPages(new WpGetPagesArgumentDto());
        $pagesOptions = new KeyValueDtoList(array_map(function ($page) {
            return new KeyValueDto((string) $this->wpFunction->getPermalink($page->ID), $page->post_title);
        }, $pages));
        $templateData['config']['pages'] = $pagesOptions;
        $templateData['data'] = array_merge([
            'selectPageType' => 'selection_of_sites_per_post_type',
        ], $postData);

        $templateData['localized']['thingsToKnow']['numberOfSelectionOfSitesPerPostType'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['numberOfSelectionOfSitesPerPostType'],
            [
                'numberOfSelectionOfSitesPerPostType' => (string) count($this->cloudScanService->getListOfPagesByType(
                    'selection_of_sites_per_post_type',
                    false,
                )),
            ],
        );

        return $this->template->getEngine()->render(
            'cloud-scan/create-cloud-scan.html.twig',
            $templateData,
        );
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewDetails(int $id): ?string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = CloudScanDetailsLocalizationStrings::get();
        $templateData['localized']['api'] = $this->cloudScanApiLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['library'] = LibraryLocalizationStrings::get();
        $templateData['localized']['restClient'] = RestClientLocalizationStrings::get();
        $this->borlabsCookieGlobalsService->addProperty('restClientLocalizationStrings', RestClientLocalizationStrings::get());
        $templateData['data']['scan'] = $this->cloudScanRepository->findById(
            $id,
            [
                'cookies' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->addOrderBy(
                        new DirectionExpression(
                            new ModelFieldNameExpression('name'),
                            new DirectionAscExpression(),
                        ),
                    );
                },
                'externalResources' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->addOrderBy(
                        new DirectionExpression(
                            new ModelFieldNameExpression('hostname'),
                            new DirectionAscExpression(),
                        ),
                    );
                },
            ],
        );

        $templateData['data']['scan']->pages->sortListByPropertyNaturally('url');
        $templateData['data']['suggestions'] = $this->cloudScanSuggestionRepository->find(['cloudScanId' => $id,]);
        $packageKeys = array_column($templateData['data']['suggestions'], 'borlabsServicePackageKey');
        $templateData['data']['packages'] = [];

        if (count($packageKeys)) {
            $templateData['data']['packages'] = $this->packageRepository->find([
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('borlabsServicePackageKey'),
                    'IN',
                    new ListExpression(
                        array_map(
                            fn ($packageKey) => new LiteralExpression($packageKey),
                            $packageKeys,
                        ),
                    ),
                ),
            ], [
                'name' => 'ASC',
            ]);
        }

        $templateData['data']['componentTypes'] = array_column(ComponentTypeEnum::getAll(), 'description', 'value');

        return $this->template->getEngine()->render(
            'cloud-scan/details-cloud-scan.html.twig',
            $templateData,
        );
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = CloudScanOverviewLocalizationStrings::get();
        $templateData['localized']['createScan'] = CloudScanCreateLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['scans'] = $this->cloudScanRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            [],
            ['createdAt' => 'DESC',],
            [],
            10,
            [],
        );

        return $this->template->getEngine()->render(
            'cloud-scan/overview-cloud-scan.html.twig',
            $templateData,
        );
    }
}
