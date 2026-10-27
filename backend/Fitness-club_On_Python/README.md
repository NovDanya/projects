# 🏋️‍♂️ Fitness Club Backend API & Management System

Профессиональный бэкенд и панель управления для автоматизации работы фитнес-клуба (клиенты, тренеры, абонементы, аналитика). Проект разработан на Python/Django по стандартам промышленной разработки: Clean Architecture, REST API, асинхронные задачи, тесты и контейнеризация.

---

## 🛠️ Технологический стек

* **Язык:** Python 3.12
* **Фреймворк:** Django 5.1.5, Django REST Framework (DRF)
* **База данных:** PostgreSQL 15
* **Очереди и фоновые задачи:** Celery + Redis
* **Тестирование:** Pytest, Pytest-Django
* **Документация API:** OpenAPI 3.0, Swagger UI (`drf-spectacular`), Redoc
* **Инфраструктура:** Docker, Docker Compose

---

## 📁 Структура проекта

```text
club_project/
├── club_project/          # Основные настройки Django и Celery
│   ├── celery.py          # Конфигурация Celery
│   ├── settings.py        # Настройки проекта (БД, DRF, Celery, CSRF)
│   └── urls.py            # Главный роутинг (Админка, API, Swagger)
├── fitness_club/          # Основное бизнес-приложение
│   ├── migrations/        # Миграции базы данных PostgreSQL
│   ├── templates/         # HTML-шаблоны панели управления
│   ├── admin.py           # Кастомная панель администратора
│   ├── api_views.py       # DRF ViewSets (REST API контроллеры)
│   ├── models.py          # Модели БД (Member, Trainer, Subscription)
│   ├── serializers.py     # Сериализаторы DRF
│   ├── services.py        # Сервисный слой (бизнес-логика и расчет цен)
│   ├── tasks.py           # Фоновые задачи Celery (проверка просроченных подписок)
│   └── tests.py           # Юнит- и API-тесты (Pytest)
├── docker-compose.yml     # Оркестрация сервисов (Web, DB, Redis, Celery)
├── Dockerfile             # Сборка Docker-образа
├── pytest.ini             # Настройки Pytest
└── requirements.txt       # Зависимости Python
```

---

## ⚠️ Важные замечания перед запуском

### 📌 1. Запуск Docker Desktop

Перед выполнением любых команд `docker-compose` убедитесь, что приложение **Docker Desktop** запущено на вашем компьютере, а его статус в трее стал зелёным (*Docker Engine running*).

### 📌 2. Доступ к сайту и ошибка CSRF (403)

Для корректной работы авторизации и защиты от CSRF открывайте проект **строго** по адресу `http://127.0.0.1:8000/`, а **НЕ** через `http://localhost:8000/`.

---

## 🚀 Развёртывание и запуск (шаг за шагом)

**1. Клонируйте репозиторий и перейдите в папку проекта:**

```bash
git clone https://github.com/NovDanya/projects.git
cd projects/backend/Fitness-club_On_Python/club_project
```

**2. Запустите сборку и контейнеры через Docker Compose:**

```bash
docker-compose up --build -d
```

Команда автоматически поднимет 4 сервиса: **PostgreSQL**, **Redis**, **Django Web** и **Celery Worker**.

**3. Примените миграции базы данных к PostgreSQL:**

```bash
docker-compose exec web python manage.py migrate
```

**4. Создайте администратора (суперпользователя):**

```bash
docker-compose exec web python manage.py createsuperuser
```

Следуйте подсказкам в консоли для ввода логина и пароля.

---

## 🌐 Доступные интерфейсы и URL

После запуска вам доступны следующие веб-адреса:

| Интерфейс | URL |
|-----------|-----|
| 🖥️ Панель управления клубом | http://127.0.0.1:8000/ |
| 🛠️ Админ-панель Django | http://127.0.0.1:8000/admin/ |
| 📑 Интерактивный Swagger UI | http://127.0.0.1:8000/api/docs/ |
| 📘 Документация Redoc | http://127.0.0.1:8000/api/redoc/ |
| 🔌 REST API (DRF Browsable API) | http://127.0.0.1:8000/api/v1/ |

---

## 🔌 Эндпоинты REST API (v1)

Все API-эндпоинты поддерживают полноценный CRUD (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`).

| Ресурс | Метод | URL | Описание |
|--------|-------|-----|----------|
| **Members** | `GET` / `POST` | `/api/v1/members/` | Список клиентов / Создание клиента |
| | `GET` / `PUT` / `DELETE` | `/api/v1/members/{id}/` | Просмотр, редактирование, удаление клиента |
| **Trainers** | `GET` / `POST` | `/api/v1/trainers/` | Список тренеров / Добавление тренера |
| | `GET` / `PUT` / `DELETE` | `/api/v1/trainers/{id}/` | Просмотр, редактирование, удаление тренера |
| **Subscriptions** | `GET` / `POST` | `/api/v1/subscriptions/` | Список абонементов / Оформление абонемента |
| | `GET` / `PUT` / `DELETE` | `/api/v1/subscriptions/{id}/` | Просмотр, редактирование, удаление абонемента |

---

## ⚡ Фоновые задачи (Celery + Redis)

В проекте реализована автоматическая фоновая задача `check_expired_subscriptions`.

**Логика работы:** задача сканирует подписки в базе данных, находит просроченные (`end_date < сегодня`) и меняет их статус на `is_active = False`.

Проверка логов Celery:

```bash
docker-compose logs celery
```

---

## 🧪 Тестирование (Pytest)

Проект покрыт юнит-тестами расчётной бизнес-логики и интеграционными API-тестами.

Для запуска тестов внутри Docker-контейнера выполните:

```bash
docker-compose exec web pytest
```

---

## 👤 Автор

Данил (NovDanya) — Backend / Fullstack Developer
