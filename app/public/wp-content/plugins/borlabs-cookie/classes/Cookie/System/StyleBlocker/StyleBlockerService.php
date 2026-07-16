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

namespace Borlabs\Cookie\System\StyleBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\Validator\StyleBlocker\StyleBlockerValidator;

class StyleBlockerService
{
    private StyleBlockerRepository $styleBlockerRepository;

    private StyleBlockerValidator $styleBlockerValidator;

    private WpFunction $wpFunction;

    public function __construct(
        StyleBlockerRepository $styleBlockerRepository,
        StyleBlockerValidator $scriptBlockerValidator,
        WpFunction $wpFunction
    ) {
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->styleBlockerValidator = $scriptBlockerValidator;
        $this->wpFunction = $wpFunction;
    }

    public function getGlobalJavaScriptForContentBlocker(StyleBlockerModel $styleBlockerModel): string
    {
        return <<<EOT
window.BorlabsCookie.Unblock.unblockStyleBlockerId('{$styleBlockerModel->key}');
EOT;
    }

    public function getOptInScriptTagForService(StyleBlockerModel $styleBlockerModel): string
    {
        return <<<EOT
<script>window.BorlabsCookie.Unblock.unblockStyleBlockerId('{$styleBlockerModel->key}');</script>
EOT;
    }

    public function save(int $id, array $postData): ?int
    {
        if (!$this->styleBlockerValidator->isValid($postData)) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/styleBlocker/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            /** @var StyleBlockerModel $existingModel */
            $existingModel = $this->styleBlockerRepository->findById($id);
        }

        $handles = new KeyValueDtoList();
        $phrases = new KeyValueDtoList();

        if (isset($postData['matchedHandles']) && is_array($postData['matchedHandles'])) {
            $this->processHandleData($postData['matchedHandles'], $handles);
        }

        if (isset($postData['matchedTags']) && is_array($postData['matchedTags'])) {
            $this->processTagData($postData['matchedTags'], $phrases);
        }

        if (isset($postData['unmatchedHandles']) && is_array($postData['unmatchedHandles'])) {
            $this->processHandleData($postData['unmatchedHandles'], $handles);
        }

        if (isset($postData['unmatchedTags']) && is_array($postData['unmatchedTags'])) {
            $this->processTagData($postData['unmatchedTags'], $phrases);
        }

        if (!isset($postData['matchedHandles']) && !isset($postData['unmatchedHandles'])) {
            $handles = $existingModel->handles ?? $handles;
        }

        if (!isset($postData['matchedTags']) && !isset($postData['unmatchedTags'])) {
            $phrases = $existingModel->phrases ?? $phrases;
        }

        $newModel = new StyleBlockerModel();
        $newModel->id = $id;
        $newModel->handles = $handles;
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->name = $postData['name'];
        $newModel->phrases = $phrases;
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->styleBlockerRepository->update($newModel);
        } else {
            $newModel = $this->styleBlockerRepository->insert($newModel);
        }

        return $newModel->id;
    }

    private function processHandleData(array $handleDataArray, KeyValueDtoList $handleList)
    {
        foreach ($handleDataArray as $handleData) {
            if (!isset($handleData['blockStatus'])) {
                continue;
            }

            $handle = key($handleData['blockStatus']);
            $status = (bool) current($handleData['blockStatus']);

            if ($status) {
                $handleList->add(new KeyValueDto($handle, $handle));
            }
        }
    }

    private function processTagData(array $tagDataArray, KeyValueDtoList $phraseList)
    {
        foreach ($tagDataArray as $index => $tagData) {
            if (!isset($tagData['blockStatus'])) {
                continue;
            }

            $status = (bool) $tagData['blockStatus'];

            if (!$status || !isset($tagData['phrase']) || trim($tagData['phrase']) === '') {
                continue;
            }

            $phraseList->add(new KeyValueDto('phrase_key_' . $index, trim($tagData['phrase'])));
        }
    }
}
