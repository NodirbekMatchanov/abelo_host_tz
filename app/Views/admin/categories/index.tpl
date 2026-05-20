{extends file="admin/layout.tpl"}

{block name="content"}

<div class="admin-header">
    <h2>Категории</h2>
    <a href="/admin/categories/create" class="btn btn--primary">+ Добавить категорию</a>
</div>

{if $categories}
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Название</th>
            <th>Описание</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $categories as $cat}
        <tr>
            <td>{$cat->id}</td>
            <td>{$cat->name|escape}</td>
            <td>{$cat->description|escape|truncate:60:'...'}</td>
            <td class="table-actions">
                <a href="/admin/categories/{$cat->id}/edit" class="btn btn--sm">Редактировать</a>
                <a href="/category/{$cat->id}" target="_blank" class="btn btn--sm">Открыть</a>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{else}
    <p class="admin-empty">Категорий пока нет. <a href="/admin/categories/create">Добавить первую</a></p>
{/if}

{/block}
