<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__stats m-0 w-100 text-center">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item cursor-default box-shadow-0 p-0">
                    <div class="kt-portlet__head kt-portlet__space-x">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'binh_luan_va_danh_gia')}
                            </h3>
                        </div>
                    </div>
                {if !empty($list_comment) || !empty($list_rating)}
                    <div class="kt-portlet__body comment_dashboard-over-flow text-left">
                        <div class="kt-widget3 ">
                            {if !empty($list_comment)}
                                {foreach from = $list_comment key = key item = item_comment}
                                    {assign var = type_comment value = "{if !empty($item_comment.type_comment == 'comment')}{__d('admin', 'binh_luan')}{else}{__d('admin', 'danh_gia')}{/if}"}
                                    {assign var = status value = "{if !empty($item_comment.status) && $item_comment.status == 2}{__d('admin', 'cho_duyet')}{/if}"}
                                    {if empty($item_comment.status)}
                                        {$status = "{__d('admin', 'khong_duyet')}"}
                                    {/if}
                                    <div class="kt-widget3__item row">
                                        <div class="col-md-4">
                                            <div class="kt-widget3__info pl-0 mb-2">
                                                {if !empty($item_comment.full_name)}
                                                    <div class="d-flex {if !empty($item_comment.is_admin)}mb-2{/if}">
                                                        <span class="kt-widget3__username fw-600">
                                                            {$item_comment.full_name} 
                                                        </span>
                                                        {if !empty($item_comment.is_admin)}
                                                            <span class="kt-badge kt-badge--danger kt-badge--inline ml-2">
                                                                Admin
                                                            </span>
                                                        {/if}
                                                    </div>
                                                {/if}
                                                {if !empty($item_comment.created)}
                                                    <span class="kt-widget3__time fs-12 font-italic">
                                                        {$this->UtilitiesAdmin->convertIntgerToDateTimeString($item_comment.created)}
                                                    </span>
                                                {/if}
                                            </div>
                                            {assign var = type value = "{if !empty($item_comment.type == 'product_detail')}{__d('admin', 'san_pham')}{else}{__d('admin', 'bai_viet')}{/if}"}

                                            {if !empty($item_comment.type)}
                                                <a class="kt-widget3__text mb-2 d-inline-block cursor-p" nh-btn="view-admin-comment" data-type="{$item_comment.type}">
                                                    <i class="fa fa-external-link-alt"></i> {$type}
                                                </a>
                                            {/if}
                                            <input type="hidden" name="foreign_id" value="{if !empty($item_comment.foreign_id)}{$item_comment.foreign_id}{/if}">
                                        </div>
                                        <div class="col-md-7">
                                            {if !empty($type_comment)}
                                                <p class="d-flex font-weight-bold kt-widget3__text mb-2">
                                                    <i class="flaticon2-talk text-warning mr-1"></i> {$type_comment}
                                                {if $item_comment.status != 1} 
                                                    <span class="kt-badge {if $item_comment.status == 2}kt-badge--success{else}kt-badge--warning{/if} kt-badge--inline fs-10 ml-2">
                                                        {$status}
                                                    </span>
                                                {/if}
                                                </p>
                                            {/if}

                                            {if !empty($item_comment.content)}
                                                <p class="kt-widget3__text text-justify content-comment mh-80 ">
                                                    {strip_tags($item_comment.content)}
                                                </p>
                                                <span nh-btn ="show-more" class=" mb-2 d-none kt-badge kt-badge--inline kt-badge--unified-brand kt-badge--bold cursor-p">
                                                    {__d('admin', 'xem_them')}
                                                </span>
                                            {/if}
                                            {if !empty($item_comment.images)}
                                                <div class="album-images list-image-album album-comment-dashboard mb-2">
                                                    {foreach from = $item_comment.images|@json_decode item = image}
                                                        <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative">
                                                            <img class="image-comment1" src="{CDN_URL}{$image}"/>
                                                        </a>
                                                    {/foreach}
                                                </div> 
                                            {/if}
                                            {assign var="name_replied" value=""}
                                            {if !empty($item_comment.parent_id)}
                                                {if !empty($list_comment[$item_comment.parent_id].full_name)}
                                                    {assign var="name_replied" value=$list_comment[$item_comment.parent_id].full_name}
                                                {/if}
                                                <div class="inner-reply mb-2">
                                                    <i class="flaticon-reply"></i>
                                                    {__d('admin', 'tra_loi')} {strip_tags($name_replied)}
                                                </div>
                                            {/if}
                                        </div>
                                        <div class="col-md-1">
                                            <a class="kt-widget3__status kt-font-info" nh-btn="view-comment" href="{if !empty($item_comment.url)}/{$item_comment.url}{/if}" target="_blank">                                    
                                                <i class="fa fa-eye cursor-p fs-14 text-primary"></i>
                                            </a>
                                        </div>                        
                                    </div>
                                {/foreach}
                            {/if}
                            {if !empty($list_rating)}
                                {foreach from = $list_rating item = item_rating}
                                    {assign var = type_comment value = "{if !empty($item_rating.type_comment == 'comment')}{__d('admin', 'binh_luan')}{else}{__d('admin', 'danh_gia')}{/if}"} 
                                    {assign var = status value = "{if !empty($item_rating.status) && $item_rating.status == 2}{__d('admin', 'cho_duyet')}{/if}"}
                                    {if empty($item_rating.status)}
                                        {$status = "{__d('admin', 'khong_duyet')}"}
                                    {/if}
                                    <div class="kt-widget3__item row">
                                        <div class="col-md-4">
                                            <div class="kt-widget3__info pl-0 mb-2">
                                                <div class="d-flex {if !empty($item_rating.is_admin)}mb-2{/if}">
                                                    <span class="kt-widget3__username fw-600">
                                                        {$item_rating.full_name} 
                                                    </span>
                                                    {if !empty($item_rating.is_admin)}
                                                        <span class="kt-badge kt-badge--danger kt-badge--inline ml-2">
                                                            Admin
                                                        </span>
                                                    {/if}
                                                </div>
                                                {if !empty($item_rating.created)}
                                                    <span class="kt-widget3__time fs-12 font-italic">
                                                        {$this->UtilitiesAdmin->convertIntgerToDateTimeString($item_rating.created)}
                                                    </span>
                                                {/if}
                                            {assign var = type value = "{if !empty($item_rating.type == 'product_detail')}{__d('admin', 'san_pham')}{else}{__d('admin', 'bai_viet')}{/if}"}
                                            </div>
                                            {if !empty($item_rating.type)}
                                                <a class="kt-widget3__text mb-2 d-inline-block cursor-p" nh-btn="view-admin-comment" data-type="{$item_rating.type}">
                                                    <i class="fa fa-external-link-alt"></i> {$type}
                                                </a>
                                            {/if}
                                            <input type="hidden" name="foreign_id" value="{if !empty($item_rating.foreign_id)}{$item_rating.foreign_id}{/if}">
                                        </div>
                                        <div class="col-md-7">
                                            {if !empty($type_comment)}
                                                <p class="d-flex font-weight-bold kt-widget3__text mb-2">
                                                    <i class="flaticon2-talk text-warning mr-1"></i> {$type_comment} 
                                                {if $item_rating.status != 1} 
                                                    <span class="kt-badge {if $item_rating.status == 2}kt-badge--success{else}kt-badge--warning{/if} kt-badge--inline fs-10 ml-2">
                                                        {$status}
                                                    </span>
                                                {/if}
                                                </p>
                                            {/if}

                                            {if !empty($item_rating.rating)}
                                                <div class="star-rating" number-rating="{$item_rating.rating}">
                                                    <span style="width:100%"></span>
                                                </div>
                                            {/if}

                                            {if !empty($item_rating.content)}
                                                <p class="kt-widget3__text text-justify content-comment mh-80 ">
                                                    {strip_tags($item_rating.content)} 
                                                </p>
                                                <span nh-btn ="show-more" class=" mb-2 d-none kt-badge kt-badge--inline kt-badge--unified-brand kt-badge--bold cursor-p">
                                                    {__d('admin', 'xem_them')}
                                                </span>
                                            {/if}
                                            {if !empty($item_rating.images)}
                                                <div class="album-images list-image-album album-comment-dashboard mb-2">
                                                    {foreach from = $item_rating.images|@json_decode item = image}
                                                        <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative">
                                                            <img class="image-comment1" src="{CDN_URL}{$image}"/>
                                                        </a>
                                                    {/foreach}
                                                </div> 
                                            {/if}
                                            {assign var="name_replied" value=""}
                                            {if !empty($item_rating.parent_id)}
                                                {if !empty($list_rating[$item_rating.parent_id].full_name)}
                                                    {assign var="name_replied" value=$list_rating[$item_rating.parent_id].full_name}
                                                {/if}
                                                <div class="inner-reply mb-2">
                                                    <i class="flaticon-reply"></i>
                                                        {__d('admin', 'tra_loi')} {strip_tags($name_replied)}
                                                </div>
                                            {/if}
                                        </div>
                                        <div class="col-md-1">
                                            <a class="kt-widget3__status kt-font-info" nh-btn="view-comment" href="{if !empty($item_rating.url)}/{$item_rating.url}{/if}" target="_blank">                                   
                                                 <i class="fa fa-eye cursor-p fs-14 text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                {else}
                    <div class="kt-portlet__body">
                        <div class="kt-font-bolder">
                            {__d('admin', 'khong_co_binh_luan')}
                        </div>
                    </div>
                {/if}
                </div>
            </div>
        </div>
    </div>
</div>