<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Requests\AuthorStoreRequest;
use App\Http\Requests\AuthorUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthorController extends Controller
{
    public function __construct(
        protected AuthorService $authorService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $authors = $this->authorService->getAuthorList();
            return ApiResponse::paginated($authors);
        } catch (Exception $e) {
            Log::error('Ошибка при получении списка авторов', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить авторов', 500);
        }
    }

    public function store(AuthorStoreRequest $request): JsonResponse
    {
        try {
            $author = $this->authorService->createNewAuthor($request->validated());
            return ApiResponse::success($author, 'Автор создан', 201);
        } catch (Exception $e) {
            Log::error('Ошибка при создании автора', ['exception' => $e]);
            return ApiResponse::error('Не удалось создать автора', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $author = $this->authorService->getAuthorById($author);
            return ApiResponse::success($author);
        } catch (ModelNotFoundException $e) {
            Log::warning('Автор не найден', ['id' => $id]);
            return ApiResponse::error('Автор не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при получении автора', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить автора', 500);
        }
    }

    public function update(AuthorUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $updatedAuthor = $this->authorService->updateAuthor($author, $request->validated());
            return ApiResponse::success($updatedAuthor, 'Автор обновлен');
        } catch (ModelNotFoundException $e) {
            Log::warning('Автор для обновления не найден', ['id' => $id]);
            return ApiResponse::error('Автор не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при обновлении автора', ['exception' => $e]);
            return ApiResponse::error('Не удалось обновить автора', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $name = $this->authorService->deleteAuthor($author);
            return ApiResponse::success(null, "Автор '{$name}' удален");
        } catch (ModelNotFoundException $e) {
            Log::warning('Автор для удаления не найден', ['id' => $id]);
            return ApiResponse::error('Автор не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при удалении автора', ['exception' => $e]);
            return ApiResponse::error('Не удалось удалить автора', 500);
        }
    }

    public function books(int $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $books = $this->authorService->getAuthorBooks($author);
            return ApiResponse::paginated($books);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Автор не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при получении книг автора', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить книги автора', 500);
        }
    }
}
