{extends file="layouts/main.tpl"}

{block name="content"}

<article class="post-full">

    {if $post->image}
        <img class="post-hero" src="{$post->image|escape}" alt="{$post->title|escape}">
    {/if}

    <header class="post-header">
        <h1>{$post->title|escape}</h1>
        <p class="post-meta">
            {$post->createdAt|date_format:"%d.%m.%Y"}
            &bull;
            {$post->viewsCount} просмотров
        </p>

        {if $post->categories}
            <div class="post-categories">
                {foreach $post->categories as $cat}
                    <a href="/category/{$cat->id}" class="tag">{$cat->name|escape}</a>
                {/foreach}
            </div>
        {/if}
    </header>

    <div class="post-content">
        {$post->content}
    </div>

</article>

{/block}
