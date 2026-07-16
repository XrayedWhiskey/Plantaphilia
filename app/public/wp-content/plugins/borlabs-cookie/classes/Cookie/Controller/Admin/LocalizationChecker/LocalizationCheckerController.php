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

namespace Borlabs\Cookie\Controller\Admin\LocalizationChecker;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\Localization\LocalizedClassDtoList;
use Borlabs\Cookie\System\Localization\LocalizationConsistencyCheckerService;
use Borlabs\Cookie\System\Localization\LocalizedClassesService;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;
use Throwable;

final class LocalizationCheckerController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-localization-checker';

    private LocalizedClassesService $localizationClassesService;

    private LocalizationConsistencyCheckerService $localizationConsistencyCheckerService;

    private MessageManager $messageManager;

    private Template $template;

    public function __construct(
        LocalizationConsistencyCheckerService $localizationConsistencyCheckerService,
        LocalizedClassesService $localizationClassesService,
        MessageManager $messageManager,
        Template $template
    ) {
        $this->localizationConsistencyCheckerService = $localizationConsistencyCheckerService;
        $this->localizationClassesService = $localizationClassesService;
        $this->messageManager = $messageManager;
        $this->template = $template;
    }

    public function route(RequestDto $request): ?string
    {
        try {
            return $this->viewOverview();
        } catch (Throwable $e) {
            $this->messageManager->error($e->getMessage());

            return $this->viewOverview();
        }
    }

    public function viewOverview(): string
    {
        $localizationClasses = new LocalizedClassDtoList();
        $localizationClasses->addList($this->localizationClassesService->getAllLocalizationClasses());
        $localizationClasses->addList($this->localizationClassesService->getAllLocalizedEnumClasses());
        $localizationClasses->sortListByPropertyNaturally('className');

        $templateData = [
            'localizationCheckerResults' => $this->localizationConsistencyCheckerService->getInformation(),
            'localizationClasses' => $localizationClasses,
        ];

        return $this->template->getEngine()->render(
            'localization-checker/localization-checker.html.twig',
            $templateData,
        );
    }
}
