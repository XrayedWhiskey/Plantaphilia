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

namespace Borlabs\Cookie\System\Message;

use Borlabs\Cookie\Dto\System\MessageDto;
use Borlabs\Cookie\Support\Formatter;

/**
 * This class collects various messages that are displayed to the user.
 */
final class MessageManager
{
    /**
     * @var MessageDto[]
     */
    private array $messages = [];

    public function critical(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'critical'), $context);
    }

    public function error(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'error'), $context);
    }

    public function getErrorMessages(): array
    {
        return $this->getErrorMessagesFiltered('error');
    }

    /**
     * @return MessageDto[]
     */
    public function getRaw(): array
    {
        return $this->messages;
    }

    public function getSuccessMessages(): array
    {
        return $this->getErrorMessagesFiltered('success');
    }

    public function info(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'info'), $context);
    }

    public function offer(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'offer'), $context);
    }

    public function success(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'success'), $context);
    }

    public function warning(string $message, ?array $context = null)
    {
        $this->add(new MessageDto($message, 'warning'), $context);
    }

    private function add(MessageDto $message, ?array $context = null)
    {
        if ($context !== null) {
            $message->message = Formatter::interpolate($message->message, $context);
        }

        $this->messages[] = $message;
    }

    private function getErrorMessagesFiltered(string $type): array
    {
        return array_filter($this->messages, function (MessageDto $message) use ($type): bool {
            return $message->type === $type;
        });
    }
}
