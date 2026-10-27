# 📚 Library API (Laravel)

RESTful API для управления библиотекой: книги, авторы, жанры. Проект разработан на PHP/Laravel с разделением на контроллеры, сервисный слой и версионированные маршруты (v1 и v2).

---

## 🛠️ Технологический стек

* **Язык:** PHP 8.x
* **Фреймворк:** Laravel 8+
* **База данных:** MySQL / MariaDB
* **Тестирование API:** Postman
* **Менеджер зависимостей:** Composer

---

## 📁 Структура проекта

```text
library-api/
├── app/
│   ├── Http/
│   │   └── Controllers/   # Контроллеры с CRUD-логикой
│   ├── Models/            # Eloquent-модели (Book, Author, Genre) со связями
│   └── Services/          # Сервисный слой бизнес-логики (например, BookService)
├── database/
│   ├── factories/         # Фабрики для генерации реалистичных тестовых данных
│   ├── migrations/        # Структура таблиц базы данных
│   └── seeders/           # Сидеры с демо-данными (30 авторов, 30 жанров, 50 книг)
├── routes/
│   └── api.php            # Маршруты API (версии v1 и v2)
├── .env.example           # Шаблон конфигурации окружения
└── library_API.postman_collection.json  # Коллекция запросов для Postman
```

---

## 🚀 Установка и запуск

### 1️⃣ Клонируйте репозиторий

```bash
git clone https://github.com/NovDanya/projects.git
cd projects/backend/Library_On_Laravel/library-api
```

### 2️⃣ Установите зависимости PHP

```bash
composer install
```

### 3️⃣ Настройте окружение

Скопируйте файл конфигурации:

```bash
cp .env.example .env
```

Откройте файл `.env` и настройте подключение к вашей базе данных MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=books
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 Перед миграциями создайте пустую базу данных с именем `books` (или с тем именем, которое указали в `DB_DATABASE`).

### 4️⃣ Сгенерируйте ключ приложения

```bash
php artisan key:generate
```

### 5️⃣ Запустите миграции и заполните базу демо-данными

```bash
php artisan migrate:fresh --seed
```

Команда пересоздаст таблицы и добавит **30 авторов, 30 жанров и 50 книг**.

### 6️⃣ Запустите локальный сервер разработки

```bash
php artisan serve
```

Сервер будет доступен по адресу: **http://127.0.0.1:8000**

---

## 🔌 Эндпоинты API

> Полный список маршрутов всегда можно проверить командой:
> ```bash
> php artisan route:list
> ```

### API v1 — Books

| Метод | URL | Описание |
|-------|-----|----------|
| `GET` | `/api/v1/books` | Получить список книг (с пагинацией) |
| `POST` | `/api/v1/books` | Создать новую книгу |
| `GET` | `/api/v1/books/{book}` | Получить информацию о книге по ID |
| `PUT` | `/api/v1/books/{book}` | Полностью обновить данные книги |
| `DELETE` | `/api/v1/books/{book}` | Удалить книгу |

### API v2 — Books, Authors, Genres

#### 📖 Books

| Метод | URL | Описание |
|-------|-----|----------|
| `GET` | `/api/v2/books/index` | Список книг |
| `POST` | `/api/v2/books/store` | Создание книги |
| `GET` | `/api/v2/books/show/{id}` | Получить книгу по ID |
| `PUT` | `/api/v2/books/update/{id}` | Обновить книгу |
| `DELETE` | `/api/v2/books/destroy/{id}` | Удалить книгу |

#### ✍️ Authors

| Метод | URL | Описание |
|-------|-----|----------|
| `GET` | `/api/v2/authors/index` | Список авторов |
| `POST` | `/api/v2/authors/store` | Создание автора |
| `GET` | `/api/v2/authors/show/{id}` | Получить автора по ID |
| `PUT` | `/api/v2/authors/update/{id}` | Обновить автора |
| `DELETE` | `/api/v2/authors/destroy/{id}` | Удалить автора |
| `GET` | `/api/v2/authors/{id}/books` | Получить все книги конкретного автора |

#### 🏷️ Genres

| Метод | URL | Описание |
|-------|-----|----------|
| `GET` | `/api/v2/genres/index` | Список жанров |
| `POST` | `/api/v2/genres/store` | Создание жанра |
| `GET` | `/api/v2/genres/show/{id}` | Получить жанр по ID |
| `PUT` | `/api/v2/genres/update/{id}` | Обновить жанр |
| `DELETE` | `/api/v2/genres/destroy/{id}` | Удалить жанр |
| `GET` | `/api/v2/genres/{id}/books` | Получить все книги конкретного жанра |

---

## 🧪 Тестирование через Postman

1. Откройте **Postman**.
2. Нажмите кнопку **Import** в левом верхнем углу.
3. Перетащите файл `library_API.postman_collection.json` или выберите его через **Upload Files**.

В коллекции уже настроена переменная `base_url = http://127.0.0.1:8000`. Для запросов `POST` и `PUT` во вкладке **Body** уже выбран формат **raw → JSON**.

### 📝 Примеры тел запросов

**Создание книги**

```json
{
  "title": "Название книги",
  "author_id": 1,
  "published_year": 2023,
  "genre_id": 2
}
```

**Создание автора**

```json
{
  "name": "Лев Толстой",
  "country": "Россия",
  "birth_date": "1828-09-09",
  "biography": "Великий русский писатель"
}
```

**Создание жанра**

```json
{
  "name": "Фантастика",
  "description": "Книги о будущем и технологиях"
}
```

---

## 👤 Автор

Данил (NovDanya) — Backend / Fullstack Developer
