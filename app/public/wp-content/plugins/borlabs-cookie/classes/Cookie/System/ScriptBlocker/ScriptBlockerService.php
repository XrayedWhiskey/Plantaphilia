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

namespace Borlabs\Cookie\System\ScriptBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Validator\ScriptBlocker\ScriptBlockerValidator;

class ScriptBlockerService
{
    private ScriptBlockerRepository $scriptBlockerRepository;

    private ScriptBlockerValidator $scriptBlockerValidator;

    private WpFunction $wpFunction;

    public function __construct(
        ScriptBlockerRepository $scriptBlockerRepository,
        ScriptBlockerValidator $scriptBlockerValidator,
        WpFunction $wpFunction
    ) {
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->scriptBlockerValidator = $scriptBlockerValidator;
        $this->wpFunction = $wpFunction;
    }

    public function getGlobalJavaScriptForContentBlocker(ScriptBlockerModel $scriptBlockerModel): string
    {
        return <<<EOT
window.BorlabsCookie.ScriptBlocker.allocateScriptBlockerToContentBlocker(contentBlockerData.id, '{$scriptBlockerModel->key}', 'scriptBlockerId');
window.BorlabsCookie.Unblock.unblockScriptBlockerId('{$scriptBlockerModel->key}');
EOT;
    }

    public function getOptInScriptTagForService(ScriptBlockerModel $scriptBlockerModel): string
    {
        return <<<EOT
<script>window.BorlabsCookie.Unblock.unblockScriptBlockerId('{$scriptBlockerModel->key}');</script>
EOT;
    }

    public function save(int $id, array $postData): ?int
    {
        if (!$this->scriptBlockerValidator->isValid($postData)) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/scriptBlocker/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            /** @var ScriptBlockerModel $existingModel */
            $existingModel = $this->scriptBlockerRepository->findById($id);
        }

        $handles = new KeyValueDtoList();
        $phrases = new KeyValueDtoList();
        $onExist = new KeyValueDtoList();

        if (isset($postData['matchedHandles']) && is_array($postData['matchedHandles'])) {
            $this->processHandleData($postData['matchedHandles'], $handles);
        }

        if (isset($postData['matchedTags']) && is_array($postData['matchedTags'])) {
            $this->processTagData($postData['matchedTags'], $phrases, $onExist);
        }

        if (isset($postData['unmatchedHandles']) && is_array($postData['unmatchedHandles'])) {
            $this->processHandleData($postData['unmatchedHandles'], $handles);
        }

        if (isset($postData['unmatchedTags']) && is_array($postData['unmatchedTags'])) {
            $this->processTagData($postData['unmatchedTags'], $phrases, $onExist);
        }

        if (!isset($postData['matchedHandles']) && !isset($postData['unmatchedHandles'])) {
            $handles = $existingModel->handles ?? $handles;
        }

        if (!isset($postData['matchedTags']) && !isset($postData['unmatchedTags'])) {
            $phrases = $existingModel->phrases ?? $phrases;
            $onExist = $existingModel->onExist ?? $onExist;
        }

        $newModel = new ScriptBlockerModel();
        $newModel->id = $id;
        $newModel->handles = $handles;
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->name = $postData['name'];
        $newModel->onExist = $onExist;
        $newModel->phrases = $phrases;
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->scriptBlockerRepository->update($newModel);
        } else {
            $newModel = $this->scriptBlockerRepository->insert($newModel);
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

    private function processTagData(array $tagDataArray, KeyValueDtoList $phraseList, KeyValueDtoList $onExistList)
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

            if (!isset($tagData['onExist']) || trim($tagData['onExist']) === '') {
                continue;
            }

            $onExistList->add(new KeyValueDto('phrase_key_' . $index, trim($tagData['onExist'])));
        }
    }
}
