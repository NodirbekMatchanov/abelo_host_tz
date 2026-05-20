<?php

declare(strict_types=1);

$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$pdo = new PDO(
    sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $_ENV['DB_HOST']    ?? '127.0.0.1',
        $_ENV['DB_PORT']    ?? '3306',
        $_ENV['DB_NAME']    ?? 'blog',
        $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ),
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

// ─── Categories ───────────────────────────────────────────────────────────────

$categories = [
    ['PHP',        'Статьи о языке программирования PHP: советы, паттерны, best practices.'],
    ['JavaScript', 'Всё о JS: фронтенд, Node.js, фреймворки и инструменты.'],
    ['MySQL',      'Проектирование баз данных, оптимизация запросов, индексы.'],
    ['Docker',     'Контейнеризация приложений, docker-compose, образы и сети.'],
    ['Linux',      'Администрирование серверов, bash-скрипты и системные утилиты.'],
    ['CSS',        'Вёрстка, анимации, Grid, Flexbox и адаптивный дизайн.'],
    ['Git',        'Управление версиями, ветки, merge/rebase и командная работа.'],
    ['Security',   'Веб-безопасность: SQL-инъекции, XSS, CSRF и защита от атак.'],
    ['API',        'Проектирование REST API, аутентификация, версионирование.'],
    ['Testing',    'Юнит- и интеграционное тестирование, TDD и инструменты.'],
];

$stmtCat = $pdo->prepare(
    'INSERT INTO categories (name, description, created_at) VALUES (:name, :desc, :created_at)'
);

$categoryIds = [];

foreach ($categories as $i => [$name, $desc]) {
    $createdAt = date('Y-m-d H:i:s', strtotime("-{$i} days"));
    $stmtCat->execute([':name' => $name, ':desc' => $desc, ':created_at' => $createdAt]);
    $categoryIds[] = (int) $pdo->lastInsertId();
}

echo '  seeded categories: ' . count($categoryIds) . "\n";

// ─── Posts ────────────────────────────────────────────────────────────────────

$posts = [
    [
        'title'       => 'Как писать чистый PHP-код без фреймворков',
        'description' => 'Разбираем принципы чистой архитектуры и MVC на голом PHP без лишних зависимостей.',
        'content'     => '<p>Многие разработчики думают, что без фреймворка написать хороший код невозможно. Это не так.</p><p>Главное — разделять ответственность: контроллеры тонкие, логика в сервисах, SQL только в репозиториях.</p><p>Используйте PSR-12, strict_types и понятные имена — и код будет читаемым без единого фреймворка.</p>',
        'views_count' => 342,
        'image'       => null,
    ],
    [
        'title'       => 'PDO и Prepared Statements: защита от SQL-инъекций',
        'description' => 'Почему конкатенация строк в SQL опасна и как правильно использовать PDO.',
        'content'     => '<p>SQL-инъекция — одна из самых распространённых уязвимостей веб-приложений.</p><p>Никогда не вставляйте пользовательские данные напрямую в запрос. Используйте <code>prepare()</code> и <code>bindValue()</code>.</p><p>PDO делает это удобно и безопасно из коробки.</p>',
        'views_count' => 518,
        'image'       => null,
    ],
    [
        'title'       => 'Docker Compose для PHP-разработчика',
        'description' => 'Поднимаем локальное окружение с Nginx, PHP-FPM и MySQL за 5 минут.',
        'content'     => '<p>Docker позволяет изолировать окружение и гарантировать, что у всех участников команды оно одинаковое.</p><p>Минимальный стек для PHP: <code>nginx</code>, <code>php-fpm</code> и <code>mysql</code>. Всё описывается в одном <code>docker-compose.yml</code>.</p><p>Монтируйте исходники через volume — изменения сразу доступны в контейнере.</p>',
        'views_count' => 210,
        'image'       => null,
    ],
    [
        'title'       => 'MySQL: индексы и их влияние на производительность',
        'description' => 'Как индексы ускоряют SELECT и замедляют INSERT, и когда их добавлять.',
        'content'     => '<p>Индекс — это структура данных, которая позволяет MySQL находить строки без полного сканирования таблицы.</p><p>Добавляйте индексы на поля, по которым часто делаете WHERE, ORDER BY и JOIN.</p><p>Но помните: каждый индекс замедляет вставку и обновление, поэтому не индексируйте всё подряд.</p>',
        'views_count' => 189,
        'image'       => null,
    ],
    [
        'title'       => 'Git: работа с ветками в команде',
        'description' => 'Feature-ветки, merge, rebase и как не сломать главную ветку.',
        'content'     => '<p>Главное правило: не работайте напрямую в <code>main</code>. Создавайте отдельную ветку под каждую задачу.</p><p>Merge сохраняет историю, rebase делает её линейной. Выбор зависит от соглашений команды.</p><p>Пишите понятные commit-сообщения — это документация вашей работы.</p>',
        'views_count' => 95,
        'image'       => null,
    ],
    [
        'title'       => 'CSS Grid vs Flexbox: когда что использовать',
        'description' => 'Разбираем задачи, где Grid выигрывает у Flexbox, и наоборот.',
        'content'     => '<p>Flexbox — одномерная система: строка или колонка. Grid — двумерная: строки и колонки одновременно.</p><p>Используйте Flexbox для навигации, карточек в ряд, выравнивания элементов внутри компонента.</p><p>Grid идеален для общего макета страницы с несколькими зонами.</p>',
        'views_count' => 274,
        'image'       => null,
    ],
    [
        'title'       => 'REST API: принципы проектирования',
        'description' => 'Как правильно именовать маршруты, использовать HTTP-методы и коды ответа.',
        'content'     => '<p>Ресурсы в URL должны быть существительными во множественном числе: <code>/posts</code>, <code>/users</code>.</p><p>Используйте HTTP-методы по назначению: GET — получить, POST — создать, PUT/PATCH — обновить, DELETE — удалить.</p><p>Всегда возвращайте осмысленные HTTP-коды: 200, 201, 404, 422, 500.</p>',
        'views_count' => 431,
        'image'       => null,
    ],
    [
        'title'       => 'XSS-атаки и защита в PHP',
        'description' => 'Что такое Cross-Site Scripting, как его эксплуатируют и как защититься.',
        'content'     => '<p>XSS возникает, когда пользовательский ввод попадает на страницу без экранирования.</p><p>В PHP всегда используйте <code>htmlspecialchars()</code> при выводе данных в HTML. В Smarty — фильтр <code>|escape</code>.</p><p>Content Security Policy (CSP) добавляет ещё один уровень защиты.</p>',
        'views_count' => 367,
        'image'       => null,
    ],
    [
        'title'       => 'Юнит-тестирование на PHP с PHPUnit',
        'description' => 'Пишем первые тесты, разбираемся с assertions и моками.',
        'content'     => '<p>Тест должен проверять одно конкретное поведение. Называйте методы так, чтобы по имени было понятно, что именно проверяется.</p><p>Используйте моки для изоляции зависимостей — тест не должен ходить в БД или делать HTTP-запросы.</p><p>Запускайте тесты при каждом коммите через CI.</p>',
        'views_count' => 143,
        'image'       => null,
    ],
    [
        'title'       => 'Linux: полезные команды для веб-разработчика',
        'description' => 'grep, awk, curl, systemctl и другие утилиты, которые экономят время.',
        'content'     => '<p><code>grep -r "pattern" ./src</code> — быстрый поиск по коду без IDE.</p><p><code>curl -I https://example.com</code> — проверить заголовки ответа сервера.</p><p><code>tail -f /var/log/nginx/error.log</code> — следить за логами в реальном времени.</p>',
        'views_count' => 88,
        'image'       => null,
    ],
];

$stmtPost = $pdo->prepare(
    'INSERT INTO posts (title, description, content, views_count, image, created_at)
     VALUES (:title, :description, :content, :views_count, :image, :created_at)'
);

$postIds = [];

foreach ($posts as $i => $post) {
    $createdAt = date('Y-m-d H:i:s', strtotime("-{$i} days"));
    $stmtPost->execute([
        ':title'       => $post['title'],
        ':description' => $post['description'],
        ':content'     => $post['content'],
        ':views_count' => $post['views_count'],
        ':image'       => $post['image'],
        ':created_at'  => $createdAt,
    ]);
    $postIds[] = (int) $pdo->lastInsertId();
}

echo '  seeded posts: ' . count($postIds) . "\n";

// ─── post_categories (many-to-many) ──────────────────────────────────────────
// Каждому посту назначаем 1–3 случайных категории

$stmtLink = $pdo->prepare(
    'INSERT IGNORE INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)'
);

$links = 0;

foreach ($postIds as $postId) {
    $count      = random_int(1, 3);
    $assigned   = (array) array_rand(array_flip($categoryIds), $count);

    foreach ($assigned as $categoryId) {
        $stmtLink->execute([':post_id' => $postId, ':category_id' => $categoryId]);
        $links++;
    }
}

echo "  seeded post_categories: {$links}\n";
echo "\nDone.\n";
