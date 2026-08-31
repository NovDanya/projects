<?php

namespace App\Services;

use App\Models\Author;
use App\Services\Messaging\MessagingInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AuthorService
{
    protected string $chatId;

    private const ACTION_CREATE = 'CREATE';
    private const ACTION_UPDATE = 'UPDATE';
    private const ACTION_DELETE = 'DELETE';
    private const ACTION_SHOW = 'SHOW';

    public function __construct(
        protected MessagingInterface $messaging
    ) {
        $this->chatId = config('telegram.default_chat_id', '');
    }

    public function createNewAuthor(array $data): Author
    {
        $author = Author::create($data);
        $this->sendAuthorNotification(self::ACTION_CREATE, $author);
        return $author;
    }

    public function getAuthorList(): LengthAwarePaginator
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, 'Запрошен список авторов');
        return Author::withCount('books')->paginate(10);
    }

    public function getAuthorById(Author $author): Author
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, "Просмотрен автор: {$author->name}");

        if (!$author->relationLoaded('books')) {
            $author->load(['books.genre']);
        }

        return $author;
    }

    public function updateAuthor(Author $author, array $data): Author
    {
        $oldName = $author->name;
        $author->update($data);

        $author->load(['books.genre']);

        $this->sendAuthorNotification(self::ACTION_UPDATE, $author, $oldName);
        return $author;
    }

    public function deleteAuthor(Author $author): string
    {
        $name = $author->name;
        $author->delete();
        $this->sendSimpleNotification(self::ACTION_DELETE, "Автор удален: {$name}");
        return $name;
    }

    /**
     * Получить книги автора с пагинацией
     */
    public function getAuthorBooks(Author $author): LengthAwarePaginator
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, "Запрошены книги автора: {$author->name}");
        return $author->books()->with('genre')->paginate(10);
    }

    protected function sendAuthorNotification(string $action, Author $author, ?string $oldName = null): void
    {
        $message = "<b>'{$action}'</b>\n";

        if ($action === self::ACTION_UPDATE && $oldName && $oldName !== $author->name) {
            $message .= "Старое имя: {$oldName}\n";
        }

        $message .= "Автор: {$author->name}\n";
        $message .= "Страна: " . ($author->country ?? 'Не указана') . "\n";

        if ($author->birth_date) {
            $message .= "Дата рождения: {$author->birth_date}\n";
        }

        $message .= "Количество книг: " . ($author->books_count ?? $author->books()->count());

        $this->sendSafeMessage($message);
    }

    protected function sendSimpleNotification(string $action, string $text): void
    {
        $message = "<b>'{$action}'</b>\n{$text}";
        $this->sendSafeMessage($message);
    }

    protected function sendSafeMessage(string $message): void
    {
        if (empty($this->chatId)) {
            return;
        }

        try {
            $this->messaging->sendMessage($this->chatId, $message);
        } catch (\Exception $e) {
            Log::warning('Не удалось отправить уведомление в Telegram', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
