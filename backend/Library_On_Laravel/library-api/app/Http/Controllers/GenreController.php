<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\GenreStoreRequest;
use App\Http\Requests\GenreUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\GenreService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class GenreController extends Controller
{
    public function __construct(
        protected GenreService $genreService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $genres = $this->genreService->getGenreList();
            return ApiResponse::paginated($genres);
        } catch (Exception $e) {
            Log::error('Ошибка при получении списка жанров', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить жанры', 500);
        }
    }

    public function store(GenreStoreRequest $request): JsonResponse
    {
        try {
            $genre = $this->genreService->createNewGenre($request->validated());
            return ApiResponse::success($genre, 'Жанр создан', 201);
        } catch (Exception $e) {
            Log::error('Ошибка при создании жанра', ['exception' => $e]);
            return ApiResponse::error('Не удалось создать жанр', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $genre = Genre::findOrFail($id);
            $genre = $this->genreService->getGenreById($genre);
            return ApiResponse::success($genre);
        } catch (ModelNotFoundException $e) {
            Log::warning('Жанр не найден', ['id' => $id]);
            return ApiResponse::error('Жанр не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при получении жанра', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить жанр', 500);
        }
    }

    public function update(GenreUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $genre = Genre::findOrFail($id);
            $updatedGenre = $this->genreService->updateGenre($genre, $request->validated());
            return ApiResponse::success($updatedGenre, 'Жанр обновлен');
        } catch (ModelNotFoundException $e) {
            Log::warning('Жанр для обновления не найден', ['id' => $id]);
            return ApiResponse::error('Жанр не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при обновлении жанра', ['exception' => $e]);
            return ApiResponse::error('Не удалось обновить жанр', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $genre = Genre::findOrFail($id);
            $name = $this->genreService->deleteGenre($genre);
            return ApiResponse::success(null, "Жанр '{$name}' удален");
        } catch (ModelNotFoundException $e) {
            Log::warning('Жанр для удаления не найден', ['id' => $id]);
            return ApiResponse::error('Жанр не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при удалении жанра', ['exception' => $e]);
            return ApiResponse::error('Не удалось удалить жанр', 500);
        }
    }

    public function books(int $id): JsonResponse
    {
        try {
            $genre = Genre::findOrFail($id);
            $books = $this->genreService->getGenreBooks($genre);
            return ApiResponse::paginated($books);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Жанр не найден', 404);
        } catch (Exception $e) {
            Log::error('Ошибка при получении книг жанра', ['exception' => $e]);
            return ApiResponse::error('Не удалось загрузить книги жанра', 500);
        }
    }
}
