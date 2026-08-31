<?php

namespace App\Services\Messaging;

interface MessagingInterface
{
    public function sendMessage(string $chatId, string $message): bool;
}
