<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title|default:'Blog'}</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="/" class="logo">Blog</a>

        <nav class="categories-nav">
            {foreach $categories as $cat}
                <a href="/category/{$cat->id}">{$cat->name|escape}</a>
            {/foreach}
        </nav>
    </div>
</header>

<main class="container">
    {block name="content"}{/block}
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; {$smarty.now|date_format:"%Y"} Blog</p>
    </div>
</footer>

</body>
</html>
