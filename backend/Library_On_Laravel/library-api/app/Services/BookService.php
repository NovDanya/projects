<?php

namespace App\Services;

use App\Models\Book;
use App\Services\Messaging\MessagingInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BookService
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

    public function createNewBook(array $data): Book
    {
        $book = Book::create($data);
        $this->sendBookNotification(self::ACTION_CREATE, $book);
        return $book;
    }

    public function getBookList(): LengthAwarePaginator
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, 'Запрошен список книг');
        return Book::with(['author', 'genre'])->paginate(10);
    }

    public function getBookById(Book $book): Book
    {
        $this->sendSimpleNotification(self::ACTION_SHOW, "Просмотрена книга: {$book->title}");

        if (!$book->relationLoaded('author')) {
            $book->load(['author', 'genre']);
        }

        return $book;
    }

    public function updateBook(Book $book, array $data): Book
    {
        $oldTitle = $book->title;
        $book->update($data);

        $book->load(['author', 'genre']);

        $this->sendBookNotification(self::ACTION_UPDATE, $book, $oldTitle);
        return $book;
    }

    public function deleteBook(Book $book): string
    {
        $title = $book->title;
        $book->delete();
        $this->sendSimpleNotification(self::ACTION_DELETE, "Книга удалена: {$title}");
        return $title;
    }

    protected function sendBookNotification(string $action, Book $book, ?string $oldTitle = null): void
    {
        if (!$book->relationLoaded('author')) {
            $book->load(['author', 'genre']);
        }

        $message = "<b>'{$action}'</b>\n";

        if ($action === self::ACTION_UPDATE && $oldTitle && $oldTitle !== $book->title) {
            $message .= "Старое название: {$oldTitle}\n";
        }

        $message .= "Название: {$book->title}\n";
        $message .= "Автор: {$book->author->name}\n";
        $message .= "Год: {$book->published_year}\n";
        $message .= "Жанр: " . ($book->genre?->name ?? 'Не указан');

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
