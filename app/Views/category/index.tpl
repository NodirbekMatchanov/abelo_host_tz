{extends file="layouts/main.tpl"}

{block name="content"}

<h1>{$category->name|escape}</h1>
{if $category->description}
    <p class="category-desc">{$category->description|escape}</p>
{/if}

<section class="sort-bar">
    <a href="/category/{$category->id}?sort=date"  class="{if $sort === 'date'}active{/if}">По дате</a>
    <a href="/category/{$category->id}?sort=views" class="{if $sort === 'views'}active{/if}">По популярности</a>
</section>

<div class="posts-grid">
    {foreach $posts as $post}
        <article class="post-card">
            {if $post->image}
                <img src="{$post->image|escape}" alt="{$post->title|escape}">
            {/if}
            <h2><a href="/post/{$post->id}">{$post->title|escape}</a></h2>
            <p class="post-meta">{$post->createdAt|date_format:"%d.%m.%Y"} &bull; {$post->viewsCount} просмотров</p>
            <p class="post-desc">{$post->description|escape}</p>
            <a href="/post/{$post->id}" class="read-more">Читать далее</a>
        </article>
    {foreachelse}
        <p>В этой категории пока нет статей.</p>
    {/foreach}
</div>

{include file="partials/pagination.tpl" baseUrl="/category/{$category->id}?sort={$sort}"}

{/block}
