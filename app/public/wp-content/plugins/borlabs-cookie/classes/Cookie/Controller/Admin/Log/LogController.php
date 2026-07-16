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

namespace Borlabs\Cookie\Controller\Admin\Log;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Log\LogDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\Log\LogOverviewLocalizationStrings;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Log\LogRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;

class LogController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-log';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private LogRepository $logRepository;

    private MessageManager $messageManager;

    private Template $template;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        LogRepository $logRepository,
        MessageManager $messageManager,
        Template $template
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->logRepository = $logRepository;
        $this->messageManager = $messageManager;
        $this->template = $template;
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
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
        $templateData['localized'] = LogDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $logEntry = $this->logRepository->findByIdOrFail($id);
        $templateData['data']['log'] = $logEntry;
        $templateData['data']['processHistory'] = $this->logRepository->getProcessIdHistory($logEntry->processId);

        return $this->template->getEngine()->render(
            'log/details-log.html.twig',
            $templateData,
        );
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $searchTerm = $postData['searchTerm'] ?? $getData['borlabs-search-term'] ?? null;

        $where = [];

        if ($searchTerm) {
            $where = [
                new BinaryOperatorExpression(
                    new BinaryOperatorExpression(
                        new ModelFieldNameExpression('processId'),
                        'LIKE',
                        new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
                    ),
                    'OR',
                    new BinaryOperatorExpression(
                        new BinaryOperatorExpression(
                            new ModelFieldNameExpression('level'),
                            'LIKE',
                            new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
                        ),
                        'OR',
                        new BinaryOperatorExpression(
                            new ModelFieldNameExpression('message'),
                            'LIKE',
                            new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
                        ),
                    ),
                ),
            ];
        }

        $logs = $this->logRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            $where,
            ['id' => 'DESC',],
            [],
            25,
            ['borlabs-search-term' => $searchTerm],
        );

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = LogOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['logs'] = $logs;

        return $this->template->getEngine()->render(
            'log/overview-log.html.twig',
            $templateData,
        );
    }
}
