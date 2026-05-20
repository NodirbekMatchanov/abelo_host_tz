{extends file="admin/layout.tpl"}

{block name="content"}

<div class="admin-header">
    <h2>Статьи</h2>
    <a href="/admin/posts/create" class="btn btn--primary">+ Добавить статью</a>
</div>

{if $posts}
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Заголовок</th>
            <th>Просмотры</th>
            <th>Дата</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $posts as $post}
        <tr>
            <td>{$post->id}</td>
            <td>{$post->title|escape}</td>
            <td>{$post->viewsCount}</td>
            <td>{$post->createdAt|date_format:"%d.%m.%Y"}</td>
            <td class="table-actions">
                <a href="/admin/posts/{$post->id}/edit" class="btn btn--sm">Редактировать</a>
                <a href="/post/{$post->id}" target="_blank" class="btn btn--sm">Открыть</a>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{else}
    <p class="admin-empty">Статей пока нет. <a href="/admin/posts/create">Добавить первую</a></p>
{/if}

{/block}
