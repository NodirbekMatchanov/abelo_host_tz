{extends file="admin/layout.tpl"}

{block name="content"}

<div class="admin-header">
    <h2>{if $category}Редактировать категорию{else}Новая категория{/if}</h2>
    <a href="/admin/categories" class="btn btn--sm">← Назад</a>
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

{if $category}
    {assign var="action" value="/admin/categories/{$category->id}/edit"}
    {assign var="nameVal" value=$old.name|default:$category->name}
    {assign var="descVal" value=$old.description|default:$category->description}
{else}
    {assign var="action" value="/admin/categories/create"}
    {assign var="nameVal" value=$old.name|default:''}
    {assign var="descVal" value=$old.description|default:''}
{/if}

<form method="POST" action="{$action}" class="admin-form">

    <div class="form-group">
        <label for="name">Название *</label>
        <input type="text" id="name" name="name" value="{$nameVal|escape}" required>
    </div>

    <div class="form-group">
        <label for="description">Описание <span class="hint">(необязательно)</span></label>
        <textarea id="description" name="description" rows="4">{$descVal|escape}</textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">
            {if $category}Сохранить{else}Создать{/if}
        </button>
        <a href="/admin/categories" class="btn">Отмена</a>
    </div>

</form>

{/block}
