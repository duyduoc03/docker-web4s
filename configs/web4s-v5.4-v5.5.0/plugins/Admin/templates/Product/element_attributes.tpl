{if !isset($main_category_id)}
    {assign var = main_category_id value = ''}
{/if}

{assign var = attributes value = $this->AttributeAdmin->getAttributeByMainCategory($main_category_id, PRODUCT, $lang)}

{if !empty($attributes)}
    {foreach from = $attributes item = attribute key = attribute_id}
        <div class="form-group">
            <label>
                {if !empty($attribute.name)}
                    {$attribute.name}
                {/if}
                {if !empty($attribute.required)}
                    <span class="kt-font-danger">*</span>
                {/if}
            </label>
            
            {if empty($attribute.options) && !empty($all_options.{$attribute_id})}
                {$attribute.options = $all_options.{$attribute_id}}
            {/if}

            {if !empty($attribute.code) && !empty($product.attributes.{$attribute.code}.value)}
                {$attribute.value = $product.attributes.{$attribute.code}.value}
            {/if}

            {$this->AttributeAdmin->generateInput($attribute, $lang)}
        </div>
    {/foreach}
{else}
    <div class="form-group">
        <span>
            {__d('admin', 'chua_co_thong_tin')}
        </span>
    </div>
{/if}