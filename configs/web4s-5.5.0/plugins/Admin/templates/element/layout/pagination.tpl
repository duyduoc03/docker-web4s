{assign var = page value = 1}
{assign var = pages value = 1}
{assign var = perpage value = 1}
{assign var = show_max_page value = 5}
{assign var = start value = 1}
{assign var = end value = $show_max_page}


{if !empty($pagination.page)}
    {assign var = page value = $pagination.page}
{/if}

{if !empty($pagination.pages)}
    {assign var = pages value = $pagination.pages}
{/if}         

{if $page >= $show_max_page}
    {assign var = end value = $page + 1}
{/if}

{if $end > $pages}
    {assign var = end value = $pages}
{/if}

{assign var = start value = $end - $show_max_page + 1}
{if $start < 1}
    {assign var = start value = 1}
{/if}

{if $pages > $show_max_page && $page >= $show_max_page}
    {assign var = show_first value = true}
{/if}

{if $pages > $show_max_page && $pages > $page}
    {assign var = show_last value = true}
{/if}

{if !empty($pages) && $pages > 1}
    <div nh-pagination="ajax" class="kt-pagination kt-pagination--brand">
        <ul class="kt-pagination__links">            
            {if !empty($show_first)}
                <li nh-page="1" class="kt-pagination__link--first">
                    <a href="javascript:;">
                        <i class="fa fa-angle-double-left kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if $pages > $show_max_page && $page > 1}
                <li nh-page="{$page -1}" class="kt-pagination__link--next">
                    <a href="javascript:;">
                        <i class="fa fa-angle-left kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if !empty($show_first)}
                <li>
                    <a href="javascript:;">...</a>
                </li>
            {/if}

            {for $number = $start to $end}
                {if $page == $number}
                    <li class="kt-pagination__link--active">
                        <a href="javascript:;">
                            {$number}
                        </a>
                    </li>
                {else}
                    <li nh-page="{$number}">
                        <a href="javascript:;">
                            {$number}
                        </a>
                    </li>
                {/if}
            {/for}

            {if !empty($show_last) && $end < $pages}
                <li>
                    <a href="javascript:;">...</a>
                </li>
            {/if}

            {if $pages > $show_max_page && $page < $pages}
                <li nh-page="{$page + 1}" class="kt-pagination__link--prev">
                    <a href="javascript:;">
                        <i class="fa fa-angle-right kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if !empty($show_last)}
                <li nh-page="{$pages}" class="kt-pagination__link--last">
                    <a href="javascript:;">
                        <i class="fa fa-angle-double-right kt-font-brand"></i>
                    </a>
                </li>
            {/if}                    
        </ul>                                                    
    </div>
{/if}