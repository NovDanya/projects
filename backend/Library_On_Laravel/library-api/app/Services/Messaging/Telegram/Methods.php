<?php

namespace App\Services\Messaging\Telegram;

use App\Services\Messaging\MessagingInterface;
use App\Exceptions\Telegram\TelegramException;
use Illuminate\Support\Facades\Log;

class Methods extends Connector implements MessagingInterface
{
    /**
     * @param string $chatId
     * @param string $message
     *
     * @return bool
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        try {
            $parseMode = config('telegram.parse_mode');

            if (strlen($message) > 4096) {
                $message = substr($message, 0, 4093) . '...';
            }

            $result = $this->request('sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
            ]);

            return !empty($result);
        } catch (\Exception $e) {
            Log::error('Telegram API error', [
                'exception' => $e,
                'chat_id' => $chatId,
                'message' => $message,
            ]);

            throw new TelegramException('Не удалось отправить уведомление в Telegram', 0, $e);
        }
    }
}
