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

namespace Borlabs\Cookie\Controller\Admin\CompatibilityPatches;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\CompatibilityPatch\CompatibilityPatchDetailsDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\CompatibilityPatch\CompatibilityPatchDetailsDtoList;
use Borlabs\Cookie\Localization\CompatibilityPatch\CompatibilityPatchDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\CompatibilityPatch\CompatibilityPatchOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Repository\CompatibilityPatch\CompatibilityPatchRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\CompatibilityPatch\CompatibilityPatchManager;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Template\Template;

final class CompatibilityPatchesController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-compatibility-patches';

    private CompatibilityPatchManager $compatibilityPatchManager;

    private CompatibilityPatchRepository $compatibilityPatchRepository;

    private Log $log;

    private PackageRepository $packageRepository;

    private Template $template;

    public function __construct(
        CompatibilityPatchManager $compatibilityPatchManager,
        CompatibilityPatchRepository $compatibilityPatchRepository,
        Log $log,
        PackageRepository $packageRepository,
        Template $template
    ) {
        $this->compatibilityPatchManager = $compatibilityPatchManager;
        $this->compatibilityPatchRepository = $compatibilityPatchRepository;
        $this->log = $log;
        $this->packageRepository = $packageRepository;
        $this->template = $template;
    }

    public function route(RequestDto $request): string
    {
        return $this->viewOverview($request->postData, $request->getData);
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $searchTerm = $postData['searchTerm'] ?? $getData['borlabs-search-term'] ?? null;
        $compatibilityPatchesPaginationResult = $this->compatibilityPatchRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('fileName'),
                    'LIKE',
                    new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm ?? '')),
                ),
            ],
            ['key' => 'ASC'],
            [],
            10,
            ['borlabs-search-term' => $searchTerm],
        );
        $packages = $this->packageRepository->find();
        $compatibilityPatchesDetailsList = new CompatibilityPatchDetailsDtoList();

        /** @var \Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel $compatibilityPatch */
        foreach ($compatibilityPatchesPaginationResult->data as $compatibilityPatch) {
            $validationStatus = $this->compatibilityPatchManager->validatePatch($compatibilityPatch);
            $package = Searcher::findObject($packages, 'borlabsServicePackageKey', $compatibilityPatch->borlabsServicePackageKey);

            if ($package === null) {
                $this->log->warning(
                    'Could not find package for compatibility patch.',
                    [
                        'compatibilityPatch' => $compatibilityPatch,
                    ],
                );

                continue;
            }

            $compatibilityPatchesDetailsList->add(
                new CompatibilityPatchDetailsDto(
                    $compatibilityPatch,
                    $this->compatibilityPatchManager->getPatchFile($compatibilityPatch),
                    $package,
                    $validationStatus,
                ),
            );
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = CompatibilityPatchOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = GlobalLocalizationStrings::get();
        $templateData['localized']['compatibilityPatchDetails'] = CompatibilityPatchDetailsLocalizationStrings::get();
        $templateData['data']['compatibilityPatchesDetailsList'] = $compatibilityPatchesDetailsList;
        $templateData['data']['pagination'] = $compatibilityPatchesPaginationResult;

        return $this->template->getEngine()->render(
            'compatibility-patch/overview-compatibility-patch.html.twig',
            $templateData,
        );
    }
}
