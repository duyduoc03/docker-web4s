{assign var = first_item value = []}
{if !empty($product.items[0])}
    {assign var = first_item value = $product.items[0]}
{/if}

{assign var = rating value = 0}
{if !empty($product.rating)}
    {math assign = rating equation = 'x*y' x = $product.rating y = 20} 
{/if}

<div nh-product-detail nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($first_item)}{$first_item.id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-content-detail">

    {if !empty($product.name)}
		<h1 class="product-title-detail mb-4">
			{$product.name|escape}
            <span nh-label-extend-name>{if !empty($first_item.extend_name)}({$first_item.extend_name}){/if}</span>
        </h1>
    {/if}

    {* Giá sản phẩm*}
    <div class="price mb-4">
        {if !empty($first_item.price)}
        	<span class="name price-amount">
                Giá niêm yết: 
        	</span>
        	{if empty($first_item.apply_special) && !empty($first_item.price)}
                <span nh-label-price="{$first_item.price}" class="price-amount">
                    <span nh-label-value>
                        {$first_item.price|number_format:0:".":","}
                    </span>                    
                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                </span>
            {/if}
    
            {if !empty($first_item.apply_special) && !empty($first_item.price_special)}
            	<span nh-label-price="{$first_item.price_special}" class="price-amount">
                    <span nh-label-value>
                        {$first_item.price_special|number_format:0:".":","}
                    </span>                    
                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                </span>
            {/if}
    
            {assign var = old_price value = ""}
            {assign var = show_old_price value = "d-none"}
            {if !empty($first_item.price) && !empty($first_item.apply_special)}
                {assign var = old_price value = $first_item.price}
                {assign var = show_old_price value = ""}
            {/if}
            <span nh-label-price-special="{$old_price}" class="price-amount old-price {$show_old_price}">
                <span nh-label-value>
                    {if !empty($first_item.price) && !empty($first_item.apply_special)}
                        {$first_item.price|number_format:0:".":","}
                    {/if}
                </span>
                <span class="currency-symbol">{CURRENCY_UNIT}</span>
            </span>
        {else}
            <span class="price-amount">
                Giá liên hệ
            </span>
        {/if}
    </div>
    {if !empty($product.attributes)}
        <div class="box-attributes">
            {foreach from = $product.attributes key = key item = attribute}
                {if !empty($attribute.value) && (!empty($attribute.input_type == SINGLE_SELECT) || !empty($attribute.input_type == TEXT))}
                    <div class="item mb-2">
                        {if !empty($attribute.name)}
                            <span class="name font-weight-bold mr-2">{$attribute.name}: </span>
                        {/if}
                        {if !empty($attribute.value) && !empty($attribute.input_type == SINGLE_SELECT)}
                            {assign var = attr_key value = $this->Attribute->getListOptions($key, {LANGUAGE})}
                            <span>
                                {if !empty($attr_key[$attribute['value']])}
                                    {$attr_key[$attribute['value']]}
                                {/if}
                            </span>
                        {else}
                            <span>
                                {$attribute.value}
                            </span>
                        {/if}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}
    
    {* Thuộc tính phiên bản sản phẩm*}
    {$this->element("../block/product_detail/element_attribute_item", [
        'product' => $product,
        'first_item' => $first_item
    ])}
    
    
    <div class="box-lai-thu">
        <span class="btn btn-primary" data-toggle="modal" data-target="#formLaiThu">
            <i class="fa-solid fa-calendar-days pt-1 mr-2"></i> Đăng ký lái thử
        </span>
    </div>
</div>

<div class="modal fade form-contact-phone" id="formLaiThu" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="fa-sharp fa-light fa-circle-xmark"></i>
            </button>
            <div class="modal-body">
                <div class="form">
                    <div class="inter-form">
                        <i class="fa-solid fa-calendar-days"></i>
                        <div class="title">
                    		Xin chào,
                    	</div>
                    	<div class="slogan">
                    	    Để chúng tôi có thể liên hệ và hỗ trợ bạn đặt lịch lái thử xe {if !empty($product.name)}<span class="font-weight-bold">{$product.name|escape}</span>{/if}, vui lòng điền đầy đủ thông tin bên dưới. Xin cảm ơn!
                    	</div>
                        <form nh-form-contact="3HZO5VFWIK" action="/contact/send-info" method="POST"  autocomplete="off">
                            <input name="san_pham" type="hidden" value="{if !empty($product.name)}{$product.name|escape}{/if}">
                            <div class="form-group">
                                <input name="full_name" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="Họ và tên*">
                            </div>
                            
                            <div class="form-group">
                                <input name="phone" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" data-rule-phoneVN data-msg-phoneVN="{__d('template', 'so_dien_thoai_chua_chinh_xac')}" placeholder="Số điện thoại liên hệ lại*">
                            </div>
                            
                            <div class="form-group">
                                <input name="email" type="email" class="form-control" placeholder="Email liên hệ lại*">
                            </div>
         
                            <div class="form-group">
                                <textarea name="content" maxlength="500" class="form-control" placeholder="Nội dung?"></textarea>
                            </div>
                           
                            <div class="form-group mb-0">
                                <span nh-btn-action="submit" class="btn btn-submit ">
                                    Gửi yêu cầu
                                </span>
                                <div class="go-hotline">
                                    Gọi hotline <a href="tel:0944681533">0944.681.533</a> (24/7)
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>