{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
<div class="{if !empty($col)}{$col}{else}col-lg-3 col-md-6 col-6{/if}">
{/if}
    <div nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($product.items[0])}{$product.items[0].id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-item wrp-effect-scale mt-0 mb-5 swiper-slide">
        <div class="inner-image mb-3">
            <div class="product-status">
                {if !empty($product.apply_special) && !empty($product.discount_percent)}
                    <span class="onsale">
                        -{$product.discount_percent}%
                    </span>
                {/if}
                
                {if !empty($product.featured)}
                    <span class="featured">
                        {__d('template', 'noi_bat')}
                    </span>
                {/if}
                
                {if isset($product.total_quantity_available) && $product.total_quantity_available <= 0 && !empty($data_init.product.check_quantity)}
                    <span class="out-stock">
                        {__d('template', 'het_hang')}
                    </span>
                {/if}
            </div>
            <div class="ratio-1-1">
                {if !empty($product['all_images'][0])}
                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['all_images'][0], 350)}"}
                {else}
                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}

                <a href="{$this->Utilities->checkInternalUrl($product.url)}" title="{$product.name}">
                    {$this->LazyLoad->renderImage([
                        'src' => $url_img, 
                        'alt' => $product.name, 
                        'class' => 'img-fluid rounded-8',
                        'ignore' => $ignore
                    ])}
                </a>
            </div>

            <div class="product-action">
                <a nh-btn-action="wishlist" wishlist-id="{if !empty($product.id)}{$product.id}{/if}" wishlist-type="{PRODUCT}" class="btn-product-action" href="javascript:;" title="{__d('template', 'yeu_thich')}">
                    <i class="fa-light fa-heart rounded-8"></i>
                </a>

                {if !empty($product.total_quantity_available) || empty($data_init.product.check_quantity)}

                    {assign var = add_cart value = "nh-btn-action='add-cart'"}
                    {assign var = title_cart value = "{__d('template', 'them_gio_hang')}"}
                    {if !empty($product.attributes_item_special) && ($product.number_item gte 2)}
                        {assign var = title_cart value = "{__d('template', 'xem_chi_tiet')}"}
                    {/if}

                    {assign var = link_cart value = "javascript:;"}
                    {if !empty($product.attributes_item_special) && !empty($product.url) && ($product.number_item gte 2)}
                        {assign var = link_cart value = "{$this->Utilities->checkInternalUrl($product.url)}"}
                        {assign var = add_cart value = ""}
                    {/if}

                    <a {$add_cart} class="btn-product-action" href="{$link_cart}" title="{$title_cart}">
                        <i class="fa-light fa-cart-shopping rounded-8"></i>
                    </a>                    
                {else}
                    <a class="btn-product-action" href="{$this->Utilities->checkInternalUrl($product.url)}" title="{__d('template', 'xem_chi_tiet')}">
                        <i class="fa-light fa-cart-shopping rounded-8"></i>
                    </a>
                {/if}
                {* <a nh-btn-action="compare" data-product-id="{if !empty($product.id)}{$product.id}{/if}" class="btn-product-action" href="javascript:;" title="{__d('template', 'so_sanh')}">
                    <i class="fa-light fa-retweet"></i>
                </a>

                <a nh-btn-action="quick-view" data-product-id="{if !empty($product.id)}{$product.id}{/if}" class="btn-product-action" href="javascript:;" title="{__d('template', 'xem_nhanh')}">
                    <i class="fa-light fa-eye"></i>
                </a>*}
            </div> 
        </div>
        
        <div class="inner-content">
            {if !empty($product.name)}
                <h4 class="product-title">
                    <a href="{$this->Utilities->checkInternalUrl($product.url)}">
                        {$product.name|escape|truncate:50:" ..."}
                    </a>
                </h4>
            {/if}
            
            <div class="product-rating-price">
                <div class="wrp-rating-price">
                    <div class="price">                        
                        {if !empty($product.price)}
                            <span class="price-amount">
                                {if empty($product.apply_special) && !empty($product.price)}
                                    {$product.price|number_format:0:".":","}
                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                {/if}
        
                                {if !empty($product.apply_special) && !empty($product.price_special)}
                                    {$product.price_special|number_format:0:".":","}
                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                {/if}
                            </span>
        
                            {if !empty($product.apply_special) && !empty($product.price)}
                                <span class="price-amount old-price">
                                    {$product.price|number_format:0:".":","}
                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                </span>
                            {/if}
                        {else}
                            <span class="price-amount">
                               {__d('template', 'lien_he')}
                            </span>
                        {/if}
                    </div>
                    
                    <div class="star-rating">
                        <span style="width:100%"></span>
                    </div>
                </div>
            </div>
        
        </div>      
    </div>
{if empty($is_slider)}
</div>
{/if}
{/strip}