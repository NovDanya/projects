# 📚 Library API (Laravel)

RESTful API для управления библиотекой (книги, авторы, жанры). Проект разработан с использованием Laravel.

## 🛠️ Технологический стек

- **PHP** 8.x
- **Laravel** 8+
- **MySQL** / MariaDB
- **Postman** (для тестирования API)

## 📁 Структура проекта

- `app/Models/` — Eloquent модели (`Book`, `Author`, `Genre`) с настроенными связями.
- `app/Http/Controllers/` — контроллеры с реализацией CRUD-логики.
- `app/Services/` — сервисный слой для бизнес-логики (например, `BookService`).
- `routes/api.php` — маршруты API (разделены на версии v1 и v2).
- `database/migrations/` — структура таблиц базы данных.
- `database/factories/` — фабрики для генерации реалистичных тестовых данных.
- `database/seeders/` — сидеры для наполнения БД демо-данными (30 авторов, 30 жанров, 50 книг).

## 🚀 Установка и запуск

### 1. Клонируйте репозиторий
- `git clone https://github.com/NovDanya/projects.git`
- `cd projects/backend/Library_On_Laravel/library-api`

### 2. Установите зависимости PHP
`composer install`

### 3. Настройте окружение
Скопируйте файл конфигурации:
`cp .env.example .env`

Откройте файл .env и настройте подключение к вашей базе данных MySQL:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=books`
- `DB_USERNAME=root`
- `DB_PASSWORD=`

### 4. Сгенерируйте ключ приложения
`php artisan key:generate`

### 5. Запустите миграции и заполните базу демо-данными
`php artisan migrate:fresh --seed`

Эта команда пересоздаст таблицы и добавит 30 авторов, 30 жанров и 50 книг.

### 6. Запустите локальный сервер разработки
`php artisan serve`

Сервер будет доступен по адресу:
http://127.0.0.1:8000

🌐 Эндпоинты API

API v1 (Books)
Метод	URL	Описание
`GET	/api/v1/books`	Получить список книг (с пагинацией)
`POST	/api/v1/books`	Создать новую книгу
`GET	/api/v1/books/{book}`	Получить информацию о книге по ID
`PUT	/api/v1/books/{book}`	Полностью обновить данные книги
`DELETE	/api/v1/books/{book}`	Удалить книгу

API v2 (Books, Authors, Genres)
Метод	URL	Описание
GET	/api/v2/books/index	Список книг
POST	/api/v2/books/store	Создание книги
GET	/api/v2/books/show/{id}	Получить книгу по ID
PUT	/api/v2/books/update/{id}	Обновить книгу
DELETE	/api/v2/books/destroy/{id}	Удалить книгу

GET	/api/v2/authors/index	Список авторов
POST	/api/v2/authors/store	Создание автора
GET	/api/v2/authors/show/{id}	Получить автора по ID
PUT	/api/v2/authors/update/{id}	Обновить автора
DELETE	/api/v2/authors/destroy/{id}	Удалить автора
GET	/api/v2/authors/{id}/books	Получить все книги конкретного автора

GET	/api/v2/genres/index	Список жанров
POST	/api/v2/genres/store	Создание жанра
GET	/api/v2/genres/show/{id}	Получить жанр по ID
PUT	/api/v2/genres/update/{id}	Обновить жанр
DELETE	/api/v2/genres/destroy/{id}	Удалить жанр
GET	/api/v2/genres/{id}/books	Получить все книги конкретного жанра

Полный список маршрутов всегда можно проверить командой:
php artisan route:list

🧪 Тестирование через Postman
Откройте Postman.
Нажмите кнопку Import в левом верхнем углу.
Перетащите файл library_API.postman_collection.json или выберите его через Upload Files.
В коллекции уже настроена переменная base_url = http://127.0.0.1:8000.
Для запросов POST и PUT во вкладке Body уже выбран формат raw → JSON.

Примеры тел запросов
Создание книги
{
    "title": "Название книги",
    "author_id": 1,
    "published_year": 2023,
    "genre_id": 2
}
Создание автора
{
    "name": "Лев Толстой",
    "country": "Россия",
    "birth_date": "1828-09-09",
    "biography": "Великий русский писатель"
}
Создание жанра
{
    "name": "Фантастика",
    "description": "Книги о будущем и технологиях"
}

👤 Автор - Данил (NovDanya)
Fullstack Developer
