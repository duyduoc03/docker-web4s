<div class="quick-search-result">
    {if empty($result)}
        <div class="text-muted">
            {__d('admin', 'khong_co_ket_qua_nao_phu_hop')}
        </div>
    {/if}

    {if !empty($result.products)}
        <div class="kt-font-lg text-primary kt-font-bolder mb-2">
            {__d('admin', 'san_pham')}
        </div>

        <div class="mb-10">
            {foreach from = $result.products item = product}
                {assign var = image value = ""}
                {if !empty($product.all_images[0])}
                    {$image = "{CDN_URL}{$this->UtilitiesAdmin->getThumbs($product.all_images[0], 50)}"}
                {/if}
                <a href="{ADMIN_PATH}/product/update/{if !empty($product.id)}{$product.id}{/if}" class="item-search d-flex align-items-center flex-grow-1">
                    <div class="symbol symbol-30 bg-transparent flex-shrink-0">
                        {if !empty($image)}
                            <div class="symbol-label" style="background-image:url({$image})"></div>
                        {else}
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z" fill="#000000"/>
                                    <path d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z" fill="#000000" opacity="0.3"/>
                                </g>
                            </svg>
                        {/if}

                    </div>
                    <div class="d-flex flex-column flex-fill ml-3">
                        {if !empty($product.name)}
                            <div class="title kt-font-bold text-dark text-hover-primary">
                                {$product.name}
                            </div>
                        {/if}

                        {assign var = first_item value = []}
                        {if !empty($product.items[0])}
                            {assign var = first_item value = $product.items[0]}
                        {/if}

                        <div class="price-amount d-flex">
                            {if !empty($first_item.apply_special) && !empty($first_item.price_special)}
                                <span class="mr-2">
                                    {$first_item.price_special|number_format:0:".":","}
                                </span>
                            {/if}

                            {if empty($first_item.apply_special) && !empty($first_item.price)}
                                <span class="mr-2">
                                    {$first_item.price|number_format:0:".":","}
                                </span>
                            {else}
                                <del class="text-danger">
                                    {$first_item.price|number_format:0:".":","}
                                </del>
                            {/if}
                        </div>
                    </div>
                </a>
            {/foreach}
        </div>
    {/if}
    
    {if !empty($result.articles)}
        <div class="kt-font-lg text-primary kt-font-bolder mb-2">
            {__d('admin', 'bai_viet')}
        </div>

        <div class="mb-10">
            {foreach from = $result.articles item = article}
                {assign var = image value = ""}
                {if !empty($article.image_avatar)}
                    {$image = "{CDN_URL}{$this->UtilitiesAdmin->getThumbs($article.image_avatar, 50)}"}
                {/if}

                <a href="{ADMIN_PATH}/article/update/{if !empty($article.id)}{$article.id}{/if}" class="item-search d-flex align-items-center flex-grow-1">
                    <div class="symbol symbol-30  flex-shrink-0">
                        {if !empty($image)}
                            <div class="symbol-label" style="background-image:url({$image})"></div>
                        {else}
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5"/>
                                    <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L12.5,10 C13.3284271,10 14,10.6715729 14,11.5 C14,12.3284271 13.3284271,13 12.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="#000000" opacity="0.3"/>
                                </g>
                            </svg>
                        {/if}                        
                    </div>
                    <div class="d-flex flex-column flex-fill ml-3">
                        {if !empty($article.name)}
                            <div class="title kt-font-bold text-dark text-hover-primary">
                                {$article.name}
                            </div>
                        {/if}
                        <span class="text-muted">
                            {if !empty($article.categories)}
                                {$this->CategoryAdmin->implodeListCategories($article.categories)|escape|truncate:70:" ..."}
                            {/if}
                        </span>
                    </div>
                </a>
            {/foreach}
        </div>
    {/if}

    {if !empty($result.customers)}
        <div class="kt-font-lg text-primary kt-font-bolder mb-2">
            {__d('admin', 'khach_hang')}
        </div>

        <div class="mb-10">
            {foreach from = $result.customers item = customer}
                <a href="{ADMIN_PATH}/customer/detail/{if !empty($customer.id)}{$customer.id}{/if}" class="item-search d-flex align-items-center flex-grow-1">
                    <div class="symbol symbol-30  flex-shrink-0">
                        <div class="symbol-label">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-fill ml-3">
                        {if !empty($customer.full_name)}
                            <div class="title kt-font-bold text-dark text-hover-primary">
                                {$customer.full_name}
                            </div>
                        {/if}
                        <span class="text-muted">
                            {if !empty($customer.phone)}
                                {__d('admin', 'so_dien_thoai')}:
                                {$customer.phone}
                            {elseif !empty($customer.full_address)}
                                {__d('admin', 'dia_chi')}:
                                {$customer.full_address|escape|truncate:70:" ..."}
                            {/if}
                        </span>
                    </div>
                </a>
            {/foreach}
        </div>
    {/if}

    {if !empty($result.orders)}
        <div class="kt-font-lg text-primary kt-font-bolder mb-2">
            {__d('admin', 'don_hang')}
        </div>

        <div class="mb-10">
            {foreach from = $result.orders item = order}
                <a href="{ADMIN_PATH}/order/detail/{if !empty($order.id)}{$order.id}{/if}" class="item-search d-flex align-items-center flex-grow-1">
                    <div class="symbol symbol-30  flex-shrink-0">
                        <div class="symbol-label">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                    <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                    <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                    <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                    <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                    <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                    <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-fill ml-3">
                        {if !empty($order.code)}
                            <div class="title kt-font-bold text-dark text-hover-primary">
                                {$order.code}
                            </div>
                        {/if}

                        <span class="">
                            {__d('admin', 'tong_tien')}:
                            {if !empty($order.total)}
                                {$order.total|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                            vnđ
                        </span>
                    </div>
                </a>
            {/foreach}
        </div>
    {/if}
</div>