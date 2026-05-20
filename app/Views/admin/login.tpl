<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Blog Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body admin-body--login">

<div class="login-box">
    <h1>Blog Admin</h1>

    {if $error}
        <div class="admin-alert">{$error|escape}</div>
    {/if}

    <form method="POST" action="/admin/login">
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" id="login" name="login" autofocus autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn--primary btn--block">Войти</button>
    </form>
</div>

</body>
</html>
