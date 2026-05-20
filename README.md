# Blog — PHP без фреймворков

Учебный проект: блог на PHP 8.1+, MySQL, Smarty. Лёгкая MVC-архитектура без фреймворков.

## Стек

| Слой | Технология |
|---|---|
| Язык | PHP 8.1+ |
| Шаблоны | Smarty 4 |
| База данных | MySQL 8.0 |
| Веб-сервер | Nginx 1.25 |
| Контейнеры | Docker + Docker Compose |
| Зависимости | Composer 2 |

---

## Быстрый старт

### 1. Клонировать репозиторий

```bash
git clone <repo-url> blog
cd blog
```

### 2. Создать `.env`

```bash
cp .env.example .env
```

Файл `.env` уже настроен под Docker — менять ничего не нужно:

```env
DB_HOST=db
DB_PORT=3306
DB_NAME=blog
DB_USER=blog_user
DB_PASS=secret
DB_CHARSET=utf8mb4
```

> Если запускаете без Docker — укажите свои значения подключения.

### 3. Установить зависимости (Composer)

**Через Docker** (рекомендуется — PHP уже в контейнере):

```bash
docker compose run --rm php composer install
```

**Локально** (если PHP установлен):

```bash
composer install
```

### 4. Запустить контейнеры

```bash
docker compose up -d
```

Убедитесь, что все три контейнера запустились:

```bash
docker compose ps
```

Ожидаемый вывод:

```
NAME      STATUS
nginx     running
php       running
db        running
```

### 5. Запустить миграции

Создаёт таблицы `categories`, `posts`, `post_categories` и служебную таблицу `migrations`.

```bash
docker compose exec php php database/migrate.php
```

Пример вывода:

```
  apply 001_create_tables.sql
  apply 002_add_updated_at_and_indexes.sql

Done. Applied 2 migration(s).
```

> Повторный запуск безопасен — уже применённые миграции пропускаются.

### 6. Заполнить тестовыми данными (Seeder)

Добавляет 10 категорий, 10 статей и связи между ними.

```bash
docker compose exec php php database/seed.php
```

Пример вывода:

```
  seeded categories: 10
  seeded posts: 10
  seeded post_categories: 17

Done.
```

### 7. Открыть сайт

| URL | Описание |
|---|---|
| http://localhost:8080 | Главная страница блога |
| http://localhost:8080/admin | Панель администратора |

**Данные для входа в админку:**

```
Логин:  admin
Пароль: admin
```

---

## Структура проекта

```
.
├── app/
│   ├── Controllers/        # Тонкие контроллеры
│   ├── Core/               # Router, Database, View, Pagination, Request
│   ├── Models/             # DTO-объекты (Post, Category)
│   ├── Repositories/       # SQL-запросы, PDO
│   ├── Services/           # Бизнес-логика
│   └── Views/              # Smarty-шаблоны (.tpl)
│       ├── layouts/
│       ├── home/
│       ├── category/
│       ├── article/
│       ├── admin/
│       └── errors/
├── config/                 # Конфигурация приложения и БД
├── database/
│   ├── migrate.php         # Скрипт запуска миграций
│   ├── seed.php            # Скрипт заполнения тестовыми данными
│   └── migrations/         # SQL-файлы миграций
├── docker/
│   ├── nginx/default.conf
│   └── php/Dockerfile
├── public/                 # Точка входа (index.php), CSS, статика
├── routes/web.php          # Определение маршрутов
├── bootstrap/app.php       # Загрузка окружения
├── .env.example
├── composer.json
└── docker-compose.yml
```

---

## Маршруты

### Публичные

| Метод | URL | Описание |
|---|---|---|
| GET | `/` | Главная: категории + 3 последних поста |
| GET | `/category/{id}` | Статьи категории, сортировка, пагинация |
| GET | `/post/{id}` | Страница статьи + похожие статьи |

Query-параметры: `?sort=date` / `?sort=views`, `?page=2`

### Админка

| Метод | URL | Описание |
|---|---|---|
| GET/POST | `/admin/login` | Вход |
| GET | `/admin/logout` | Выход |
| GET | `/admin` | Список статей |
| GET/POST | `/admin/posts/create` | Создать статью |
| GET/POST | `/admin/posts/{id}/edit` | Редактировать статью |
| GET | `/admin/categories` | Список категорий |
| GET/POST | `/admin/categories/create` | Создать категорию |
| GET/POST | `/admin/categories/{id}/edit` | Редактировать категорию |

---

## Архитектура

```
Request → Router → Controller → Service → Repository → MySQL
                       ↓
                     View (Smarty)
```

| Слой | Ответственность |
|---|---|
| **Controller** | Принять запрос, вызвать сервис, передать данные в шаблон |
| **Service** | Бизнес-логика: пагинация, похожие статьи, сборка данных |
| **Repository** | SQL-запросы через PDO с Prepared Statements |
| **Model** | Readonly DTO-объекты, фабричный метод `fromArray()` |
| **View** | Smarty-шаблоны, только логика отображения |

---

## Полезные команды Docker

```bash
# Запустить контейнеры
docker compose up -d

# Остановить контейнеры
docker compose down

# Посмотреть логи PHP
docker compose logs php

# Посмотреть логи Nginx
docker compose logs nginx

# Зайти в контейнер PHP
docker compose exec php sh

# Зайти в MySQL
docker compose exec db mysql -u blog_user -psecret blog

# Пересобрать образы (после изменения Dockerfile)
docker compose up -d --build
```

---

## Сброс данных и повторный запуск

Если нужно начать с чистого листа:

```bash
# Зайти в MySQL и очистить таблицы
docker compose exec db mysql -u blog_user -psecret blog \
  -e "DROP TABLE IF EXISTS post_categories, posts, categories, migrations;"

# Запустить миграции заново
docker compose exec php php database/migrate.php

# Заполнить тестовыми данными
docker compose exec php php database/seed.php
```

---

## Требования (без Docker)

- PHP 8.1+, расширения: `pdo_mysql`, `mbstring`
- MySQL 8.0+
- Composer 2
- Nginx или Apache с mod_rewrite

---

## Переменные окружения

| Переменная | Описание | По умолчанию |
|---|---|---|
| `APP_ENV` | Окружение (`local` / `production`) | `local` |
| `APP_DEBUG` | Показывать ошибки | `true` |
| `DB_HOST` | Хост MySQL | `db` |
| `DB_PORT` | Порт MySQL | `3306` |
| `DB_NAME` | Имя базы данных | `blog` |
| `DB_USER` | Пользователь БД | `blog_user` |
| `DB_PASS` | Пароль БД | `secret` |
| `DB_CHARSET` | Кодировка | `utf8mb4` |
