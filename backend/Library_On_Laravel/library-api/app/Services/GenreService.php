<?php

namespace App\Services;

use App\Models\Genre;
use App\Services\Messaging\MessagingInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class GenreService
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

    public function createNewGenre(array $data): Genre
    {
        $genre = Genre::create($data);
        $this->sendGenreNotification(self::ACTION_CREATE, $genre);
        return $genre;
    }

    public function getGenreList(): LengthAwarePaginator
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, 'Запрошен список жанров');
        return Genre::withCount('books')->paginate(10);
    }

    public function getGenreById(Genre $genre): Genre
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, "Просмотрен жанр: {$genre->name}");

        if (!$genre->relationLoaded('books')) {
            $genre->load(['books.author']);
        }

        return $genre;
    }

    public function updateGenre(Genre $genre, array $data): Genre
    {
        $oldName = $genre->name;
        $genre->update($data);

        $genre->load(['books.author']);

        $this->sendGenreNotification(self::ACTION_UPDATE, $genre, $oldName);
        return $genre;
    }

    public function deleteGenre(Genre $genre): string
    {
        $name = $genre->name;
        $genre->delete();
        $this->sendSimpleNotification(self::ACTION_DELETE, "Жанр удален: {$name}");
        return $name;
    }

    /**
     * Получить книги жанра с пагинацией
     */
    public function getGenreBooks(Genre $genre): LengthAwarePaginator
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, "Запрошены книги жанра: {$genre->name}");
        return $genre->books()->with('author')->paginate(10);
    }

    protected function sendGenreNotification(string $action, Genre $genre, ?string $oldName = null): void
    {
        $message = "<b>'{$action}'</b>\n";

        if ($action === self::ACTION_UPDATE && $oldName && $oldName !== $genre->name) {
            $message .= "Старое название: {$oldName}\n";
        }

        $message .= "Жанр: {$genre->name}\n";

        if ($genre->description) {
            $message .= "Описание: {$genre->description}\n";
        }

        $message .= "Количество книг: " . ($genre->books_count ?? $genre->books()->count());

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
