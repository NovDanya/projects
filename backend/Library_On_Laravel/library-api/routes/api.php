<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\GenreController;

Route::prefix('v1')->group(function () {
    Route::apiResource('books', BookController::class);
});

Route::prefix('v2')->group(function () {
    // Книги
    Route::get('books/index', [BookController::class, 'index']);
    Route::post('books/store', [BookController::class, 'store']);
    Route::get('books/show/{id}', [BookController::class, 'show']);
    Route::put('books/update/{id}', [BookController::class, 'update']);
    Route::patch('books/update/{id}', [BookController::class, 'update']);
    Route::delete('books/destroy/{id}', [BookController::class, 'destroy']);

    // Авторы
    Route::get('authors/index', [AuthorController::class, 'index']);
    Route::post('authors/store', [AuthorController::class, 'store']);
    Route::get('authors/show/{id}', [AuthorController::class, 'show']);
    Route::put('authors/update/{id}', [AuthorController::class, 'update']);
    Route::patch('authors/update/{id}', [AuthorController::class, 'update']);
    Route::delete('authors/destroy/{id}', [AuthorController::class, 'destroy']);
    Route::get('authors/{id}/books', [AuthorController::class, 'books']);

    // Жанры
    Route::get('genres/index', [GenreController::class, 'index']);
    Route::post('genres/store', [GenreController::class, 'store']);
    Route::get('genres/show/{id}', [GenreController::class, 'show']);
    Route::put('genres/update/{id}', [GenreController::class, 'update']);
    Route::patch('genres/update/{id}', [GenreController::class, 'update']);
    Route::delete('genres/destroy/{id}', [GenreController::class, 'destroy']);
    Route::get('genres/{id}/books', [GenreController::class, 'books']);
});
