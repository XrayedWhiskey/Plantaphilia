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

namespace Borlabs\Cookie\Controller\Admin\ConsentLog;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\ConsentLog\ConsentLogDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\ConsentLog\ConsentLogOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Repository\ConsentLog\ConsentLogRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;

class ConsentLogController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-consent-log';

    private ConsentLogDetailsLocalizationStrings $consentLogDetailsLocalizationStrings;

    private ConsentLogRepository $consentLogRepository;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private MessageManager $messageManager;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private Template $template;

    public function __construct(
        ConsentLogDetailsLocalizationStrings $consentLogDetailsLocalizationStrings,
        ConsentLogRepository $consentLogRepository,
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        ServiceRepository $serviceRepository,
        ServiceGroupRepository $serviceGroupRepository,
        Template $template
    ) {
        $this->consentLogDetailsLocalizationStrings = $consentLogDetailsLocalizationStrings;
        $this->consentLogRepository = $consentLogRepository;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->serviceRepository = $serviceRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->template = $template;
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
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

    public function viewDetails(int $id): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->consentLogDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $latestConsentLog = $this->consentLogRepository->findByIdOrFail($id);
        $templateData['data']['consentLogs'] = $this->consentLogRepository->getUidHistory($latestConsentLog->uid);
        $templateData['data']['services'] = $this->serviceRepository->getAllOfSelectedLanguage();
        $templateData['data']['serviceGroups'] = $this->serviceGroupRepository->getAllOfSelectedLanguage();

        return $this->template->getEngine()->render(
            'consent-log/details-consent-log.html.twig',
            $templateData,
        );
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $searchTerm = $postData['searchTerm'] ?? $getData['borlabs-search-term'] ?? null;

        $where = [
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('uid'),
                'LIKE',
                new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm ?? '')),
            ),
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('isLatest'),
                '=',
                new LiteralExpression(true),
            ),
        ];

        // If no search term is given we limit the data to the last 7 days
        if (!isset($searchTerm) || strlen($searchTerm) <= 7) {
            $where[] = new BinaryOperatorExpression(
                new ModelFieldNameExpression('stamp'),
                '>=',
                new LiteralExpression(date('YmdHis', strtotime('-7 days'))),
            );
        }

        $consentLogs = $this->consentLogRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            $where,
            ['stamp' => 'DESC',],
            [],
            10,
            ['borlabs-search-term' => $searchTerm],
        );

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ConsentLogOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['consentLogs'] = $consentLogs;

        return $this->template->getEngine()->render(
            'consent-log/overview-consent-log.html.twig',
            $templateData,
        );
    }
}
