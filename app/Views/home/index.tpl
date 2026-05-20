{extends file="layouts/main.tpl"}

{block name="content"}

{foreach $sections as $section}
<section class="category-section">
    <div class="category-section__header">
        <h2>{$section.category->name|escape}</h2>
        <a href="/category/{$section.category->id}" class="btn-all">Все статьи</a>
    </div>

    {if $section.category->description}
        <p class="category-desc">{$section.category->description|escape}</p>
    {/if}

    <div class="posts-grid">
        {foreach $section.posts as $post}
            <article class="post-card">
                {if $post->image}
                    <img src="{$post->image|escape}" alt="{$post->title|escape}">
                {/if}
                <h3><a href="/post/{$post->id}">{$post->title|escape}</a></h3>
                <p class="post-meta">{$post->createdAt|date_format:"%d.%m.%Y"} &bull; {$post->viewsCount} просмотров</p>
                <p class="post-desc">{$post->description|escape}</p>
                <a href="/post/{$post->id}" class="read-more">Читать далее</a>
            </article>
        {/foreach}
    </div>
</section>
{foreachelse}
    <p class="empty-msg">Статьи не найдены.</p>
{/foreach}

{/block}
