<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$pageTitle|default:'Админка'} — Blog Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <a href="/admin" class="admin-logo">Blog Admin</a>
    <nav class="admin-nav">
        <a href="/admin">Статьи</a>
        <a href="/admin/posts/create">+ Добавить статью</a>
        <a href="/admin/categories">Категории</a>
        <a href="/admin/categories/create">+ Добавить категорию</a>
    </nav>
    <a href="/admin/logout" class="admin-logout">Выйти</a>
</aside>

<main class="admin-main">
    {block name="content"}{/block}
</main>

</body>
</html>
