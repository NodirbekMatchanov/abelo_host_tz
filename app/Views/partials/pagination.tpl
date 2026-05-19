{if $pagination->totalPages > 1}
<nav class="pagination">
    {if $pagination->hasPrev()}
        <a href="{$baseUrl}&page={$pagination->currentPage - 1}">&laquo; Назад</a>
    {/if}

    {for $p = 1 to $pagination->totalPages}
        <a href="{$baseUrl}&page={$p}"
           class="{if $p === $pagination->currentPage}active{/if}">{$p}</a>
    {/for}

    {if $pagination->hasNext()}
        <a href="{$baseUrl}&page={$pagination->currentPage + 1}">Вперёд &raquo;</a>
    {/if}
</nav>
{/if}
