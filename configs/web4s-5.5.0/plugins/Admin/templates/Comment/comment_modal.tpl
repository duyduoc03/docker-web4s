{if (!empty($comment_list))}
    <div class="kt-section__info">
        <div class="form-group form-group-xs row">
            <div class="col-lg-3 col-xl-3 ">
                {__d('admin', 'ho_va_ten')}
            </div>
            <div class="col-lg-8 col-xl-8">
                {if (!empty($comment_list[0]['full_name']))}
                    <span class=" kt-font-bolder">
                        {$comment_list[0]['full_name']} 
                    </span>
                {/if}
                {if (!empty($comment_list[0]['is_admin']))}
                    <span class="kt-badge kt-badge--danger kt-badge--inline"> {__d('admin', 'quan_tri')}</span>
                {/if}
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <div class="col-lg-3 col-xl-3 ">
                {__d('admin', 'ngay_tao')}
            </div>
            <div class="col-lg-8 col-xl-8">
                {if (!empty($comment_list[0]['full_time']))}
                    <span class=" kt-font-bolder">
                        {$comment_list[0]['full_time']} 
                    </span>
                {/if}
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <div class="col-lg-3 col-xl-3 ">
                {__d('admin', 'noi_dung')}
            </div>
            <div class="col-lg-8 col-xl-8">
                {if (!empty($comment_list[0]['content']))}
                    <span class=" kt-font-bolder">
                        {$comment_list[0]['content']}
                    </span>
                {/if}
            </div>
        </div>
        
        {if (!empty($comment_list[0]['images']))}
            <div class="form-group form-group-xs row">
                <div class="col-lg-3 col-xl-3 ">
                    {__d('admin', 'hinh_anh')}
                </div>
                <div class="col-lg-8 col-xl-8">
                    <div class="d-flex flex-wrap kt-margin-t-10">
                        {foreach from = $comment_list[0]['images'] item = item}
                            <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="comment-modal" href="{CDN_URL}{$item}">
                                <img class="img-cover" src="{CDN_URL}{$item}" alt="image" style="width: 80px;">
                            </a>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
    </div>

    {if count($comment_list) > 1}
        {$comment_list = $comment_list|array_diff_key:(['0']|array_flip)}
        <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed kt-margin-t-15 kt-margin-b-15"></div>
        <div class="kt-notes__items">
            {foreach from = $comment_list item = item}
                <div class="kt-notes__item kt-notes__item--clean {if !empty($current_comment) && $current_comment == $item.id}kt-padding-b-30{else} pb-15{/if}">
                    <div class="kt-notes__media">
                        {if !empty($current_comment) && $current_comment == $item.id}
                            <span class="kt-notes__icon kt-font-boldest">
                                <i class="flaticon2-chat-1"></i>
                            </span>
                        {else}
                            <span class="kt-notes__circle"></span>
                        {/if}
                    </div>
                    <div class="kt-notes__content">
                        <div class="kt-notes__section">
                            <div class="kt-notes__info">
                                <div class="kt-notes__title">
                                    {if (!empty($item.full_name))}
                                        <span class="kt-font-bolder">
                                            {$item.full_name}
                                        </span>
                                    {/if}
                                    {if (!empty($item.full_time))}
                                        <span class="kt-notes__desc">
                                            {$item.full_time}
                                        </span>
                                    {/if}

                                    {if (!empty($item.is_admin))}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline">{__d('admin', 'quan_tri')}</span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <span class="kt-notes__body">
                            {if (!empty($item.content))}                               
                                {$item.content}
                            {/if}
                        </span>

                        {if (!empty($item.images))}
                            <div class="d-flex flex-wrap kt-margin-t-10">
                                {foreach from = $item.images item = image}
                                    <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="comment-modal-{$item.id}" href="{CDN_URL}{$image}">
                                        <img class="img-cover" src="{CDN_URL}{$image}" alt="image" style="width: 80px;">
                                    </a>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
    {/if}
{/if}