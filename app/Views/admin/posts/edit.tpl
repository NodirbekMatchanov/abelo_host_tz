{extends file="admin/layout.tpl"}

{block name="content"}

<div class="admin-header">
    <h2>Редактировать статью</h2>
    <a href="/admin" class="btn btn--sm">← Назад</a>
</div>

{if $errors}
<div class="admin-alert">
    <ul>
        {foreach $errors as $err}
            <li>{$err|escape}</li>
        {/foreach}
    </ul>
</div>
{/if}

<form method="POST" action="/admin/posts/{$post->id}/edit" class="admin-form">

    <div class="form-group">
        <label for="title">Заголовок *</label>
        <input type="text" id="title" name="title" value="{$post->title|escape}" required>
    </div>

    <div class="form-group">
        <label for="description">Краткое описание *</label>
        <textarea id="description" name="description" rows="3" required>{$post->description|escape}</textarea>
    </div>

    <div class="form-group">
        <label for="content">Текст статьи * <span class="hint">(можно использовать HTML)</span></label>
        <textarea id="content" name="content" rows="12" required>{$post->content|escape}</textarea>
    </div>

    <div class="form-group">
        <label for="image">URL изображения <span class="hint">(необязательно)</span></label>
        <input type="url" id="image" name="image" value="{$post->image|default:''|escape}" placeholder="https://...">
    </div>

    <div class="form-group">
        <label>Категории</label>
        <div class="checkbox-group">
            {foreach $categories as $cat}
                <label class="checkbox-label">
                    <input type="checkbox" name="categories[]" value="{$cat->id}"
                        {if in_array($cat->id, $selectedCategories)}checked{/if}>
                    {$cat->name|escape}
                </label>
            {/foreach}
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/post/{$post->id}" target="_blank" class="btn">Открыть статью</a>
        <a href="/admin" class="btn">Отмена</a>
    </div>

</form>

{/block}
