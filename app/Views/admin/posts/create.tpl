{extends file="admin/layout.tpl"}

{block name="content"}

<div class="admin-header">
    <h2>Новая статья</h2>
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

<form method="POST" action="/admin/posts/create" class="admin-form">

    <div class="form-group">
        <label for="title">Заголовок *</label>
        <input type="text" id="title" name="title" value="{$old.title|default:''|escape}" required>
    </div>

    <div class="form-group">
        <label for="description">Краткое описание *</label>
        <textarea id="description" name="description" rows="3" required>{$old.description|default:''|escape}</textarea>
    </div>

    <div class="form-group">
        <label for="content">Текст статьи * <span class="hint">(можно использовать HTML)</span></label>
        <textarea id="content" name="content" rows="12" required>{$old.content|default:''|escape}</textarea>
    </div>

    <div class="form-group">
        <label for="image">URL изображения <span class="hint">(необязательно)</span></label>
        <input type="url" id="image" name="image" value="{$old.image|default:''|escape}" placeholder="https://...">
    </div>

    <div class="form-group">
        <label>Категории</label>
        <div class="checkbox-group">
            {foreach $categories as $cat}
                <label class="checkbox-label">
                    <input type="checkbox" name="categories[]" value="{$cat->id}"
                        {if isset($old.categoryIds) && in_array($cat->id, $old.categoryIds)}checked{/if}>
                    {$cat->name|escape}
                </label>
            {/foreach}
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Опубликовать</button>
        <a href="/admin" class="btn">Отмена</a>
    </div>

</form>

{/block}
