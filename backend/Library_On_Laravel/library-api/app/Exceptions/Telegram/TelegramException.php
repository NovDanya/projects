<?php

namespace App\Exceptions\Telegram;

use Exception;

class TelegramException extends Exception
{
    /**
     * @param mixed string
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = "Ошибка отправки в Telegram",
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
