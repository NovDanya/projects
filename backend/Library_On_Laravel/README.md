# Library API (Laravel 12)
RESTful API для управления книгами. Тестовое задание.


## Структура проекта
app/Models/Book.php — модель с указанием таблицы library и $fillable
app/Http/Controllers/BookController.php — контроллер с полной логикой CRUD
routes/web.php — все маршруты с отключённой CSRF-защитой
database/factories/BookFactory.php — генерация демо-данных
database/seeders/BookSeeder.php — сидер для 50 книг


## Эндпоинты
- `GET /books` — список книг (с пагинацией по 10 записей)
- `POST /books` — создать новую книгу
- `GET /books/{id}` — получить книгу по ID
- `PUT /books/{id}` — обновить книгу
- `DELETE /books/{id}` — удалить книгу


## Установка и запуск
1. Клонируйте репозиторий:
  git clone https://github.com/NovDanya/projects.git
2. Перейдите в папку с проектом:
  cd projects\backend\Library_On_Laravel\library-api
3. Установите зависимости:
  composer install
4. Настройте базу данных:
  Создайте пустую базу данных MySQL (например, library)
  Скопируйте .env.example в .env
  Откройте файл .env и укажите параметры вашей БД:
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=library
      DB_USERNAME=ваш_пользователь
      DB_PASSWORD=ваш_пароль
5. Запустите миграции и заполните БД демо-данными:
  php artisan migrate --seed
6. Запустить локальный сервер:
  php artisan serve


## Тестировка Через Postman
1. В Postman нажмите ctrl+o
2. Импортируйте файл library.postman_collection.json (путь к нему projects/backend/Library_On_Laravel)
3. Протестируйте запросы
