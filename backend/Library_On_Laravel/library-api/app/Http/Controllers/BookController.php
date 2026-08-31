<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $books = $this->bookService->getBookList();
            return ApiResponse::paginated($books);
        } catch (Exception $e) {
            Log::error('Ошибка при получении списка книг', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить книги', 500);
        }
    }

    public function store(BookStoreRequest $request): JsonResponse
    {
        try {
            $book = $this->bookService->createNewBook($request->validated());
            $book->load(['author', 'genre']);
            return ApiResponse::success($book, 'Книга создана', 201);
        } catch (Exception $e) {
            Log::error('Ошибка при создании книги', ['exception' => $e]);
            return ApiResponse::error('Не удалось создать книгу', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book = $this->bookService->getBookById($book);
            return ApiResponse::success($book);
        } catch (ModelNotFoundException $e) {
            Log::warning('Книга не найдена', ['id' => $id]);
            return ApiResponse::error('Книга не найдена', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при получении книги', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить книгу', 500);
        }
    }

    public function update(BookUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $updatedBook = $this->bookService->updateBook($book, $request->validated());
            return ApiResponse::success($updatedBook, 'Книга обновлена');
        } catch (ModelNotFoundException $e) {
            Log::warning('Книга для обновления не найдена', ['id' => $id]);
            return ApiResponse::error('Книга не найдена', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при обновлении книги', ['exception' => $e]);
            return ApiResponse::error('Не удалось обновить книгу', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $title = $this->bookService->deleteBook($book);
            return ApiResponse::success(null, "Книга '{$title}' удалена");
        } catch (ModelNotFoundException $e) {
            Log::warning('Книга для удаления не найдена', ['id' => $id]);
            return ApiResponse::error('Книга не найдена', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при удалении книги', ['exception' => $e]);
            return ApiResponse::error('Не удалось удалить книгу', 500);
        }
    }
}
