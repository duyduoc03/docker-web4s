{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}

{if !empty($product.attributes)}
    {assign var = product_attr value = $product.attributes}
{/if}

{if empty($is_slider)}
<div class="{if !empty($col)}{$col}{else}col-lg-4 col-md-6 col-12 mb-lg-5 mb-4{/if}">
{/if}

    <div nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($product.items[0])}{$product.items[0].id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-item wrp-effect-scale swiper-slide bg-white">
        <div class="inner-image">
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
            <div class="ratio-16-9">
                {if !empty($product['attributes']['anhchinh']['value'])}
                    {assign var = url_img value = "{CDN_URL}{$product['attributes']['anhchinh']['value']}"}
                {elseif !empty($product['all_images'][0])}
                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['all_images'][0], 500)}"}
                {else}
                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}

                <a href="{$this->Utilities->checkInternalUrl($product.url)}" title="{$product.name}">
                    {$this->LazyLoad->renderImage([
                        'src' => $url_img, 
                        'alt' => $product.name, 
                        'class' => 'img-fluid',
                        'ignore' => $ignore
                    ])}
                </a>
            </div>
        </div>
        
        <div class="inner-content text-center">
            {if !empty($product.name)}
                <h4 class="product-title">
                    <a href="{$this->Utilities->checkInternalUrl($product.url)}">
                        {$product.name|escape|truncate:50:" ..."}
                    </a>
                </h4>
            {/if}

            <div class="price mt-3">    
                {if !empty($product.price)}
                    <span class="price-amount mr-2">
                        Giá niêm yết:
                    </span>
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
                        Giá: liên hệ
                    </span>
                {/if}
            </div>
            
            {if !empty($product_attr.quangduong.value)}
                <div class="attributes-plus">
                    {if !empty($product_attr.quangduong.value)}
                        <div class="item-attr">
                            <span class="title">
                                {$product_attr.quangduong.name}:
                            </span>
                            <span>{$product_attr.quangduong.value}</span>
                        </div>
                    {/if}
                    
                    {if !empty($product_attr.phankhuc.value)}
                        <div class="item-attr">
                            <span class="title">
                                {$product_attr.phankhuc.name}:
                            </span>
                            {assign var = attr_phankhuc value = $this->Attribute->getListOptions('phankhuc', {LANGUAGE})}
                            <span>
                                {if !empty($attr_phankhuc[$product_attr.phankhuc.value])}
                                    {$attr_phankhuc[$product_attr.phankhuc.value]}
                                {/if}
                            </span>
                        </div>
                    {/if}
                    
                    {if !empty($product_attr.socho.value)}
                        <div class="item-attr">
                            <span class="title">
                                {$product_attr.socho.name}:
                            </span>
                            {assign var = attr_socho value = $this->Attribute->getListOptions('socho', {LANGUAGE})}
                            <span>
                                {if !empty($attr_socho[$product_attr.socho.value])}
                                    {$attr_socho[$product_attr.socho.value]}
                                {/if}
                            </span>
                        </div>
                    {/if}
                    
                </div>
            {/if}
        </div>      
    </div>
{if empty($is_slider)}
</div>
{/if}
{/strip}