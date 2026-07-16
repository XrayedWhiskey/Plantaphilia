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

namespace Borlabs\Cookie\Controller\Admin\Job;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Job\JobDetailsLocalizationStrings;
use Borlabs\Cookie\Localization\Job\JobOverviewLocalizationStrings;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ContainsLikeLiteralExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Job\JobRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;

class JobController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-job';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private JobRepository $jobRepository;

    private MessageManager $messageManager;

    private Template $template;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        JobRepository $jobRepository,
        MessageManager $messageManager,
        Template $template
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->jobRepository = $jobRepository;
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
        $templateData['localized'] = JobDetailsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $jobEntry = $this->jobRepository->findByIdOrFail($id);
        $templateData['data']['job'] = $jobEntry;

        return $this->template->getEngine()->render(
            'job/details-job.html.twig',
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
                        new ModelFieldNameExpression('payload'),
                        'LIKE',
                        new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
                    ),
                    'OR',
                    new BinaryOperatorExpression(
                        new ModelFieldNameExpression('type'),
                        'LIKE',
                        new ContainsLikeLiteralExpression(new LiteralExpression($searchTerm)),
                    ),
                ),
            ];
        }

        $jobs = $this->jobRepository->paginate(
            (int) ($getData['borlabs-page'] ?? 1),
            $where,
            ['id' => 'DESC',],
            [],
            25,
            ['borlabs-search-term' => $searchTerm],
        );

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = JobOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['jobs'] = $jobs;

        return $this->template->getEngine()->render(
            'job/overview-job.html.twig',
            $templateData,
        );
    }
}
