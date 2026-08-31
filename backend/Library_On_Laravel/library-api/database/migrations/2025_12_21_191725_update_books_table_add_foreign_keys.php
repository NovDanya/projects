<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // Удаляем старые поля
            $table->dropColumn(['author', 'genre']);

            // Добавляем внешние ключи
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->foreignId('genre_id')->nullable()->constrained('genres')->onDelete('set null');

            // Можно добавить индексы для производительности
            $table->index('author_id');
            $table->index('genre_id');
            $table->index('published_year');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropForeign(['genre_id']);
            $table->dropColumn(['author_id', 'genre_id']);
            $table->string('author')->nullable();
            $table->string('genre')->nullable();
        });
    }
};
