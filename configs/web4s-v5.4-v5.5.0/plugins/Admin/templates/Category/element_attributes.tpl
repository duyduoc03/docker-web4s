{if !empty($all_attributes)}
    {foreach from = $all_attributes item = attribute key = attribute_id}
        <div class="form-group">
            <label>
                {if !empty($attribute.name)}
                    {$attribute.name}
                {/if}

                {if !empty($attribute.required)}
                    <span class="kt-font-danger">*</span>
                {/if}
            </label>

            {if !empty($all_options[$attribute_id])}
                {$attribute.options = $all_options[$attribute_id]}
            {/if}

            {if !empty($attribute.code) && !empty($category.attributes[$attribute.code])}
                {$attribute.value = $category.attributes[$attribute.code]}
            {/if}

            {if (!empty($attribute.attribute_type) && 
                $attribute.attribute_type == CATEGORY) && 
                (!empty($attribute.input_type) && ($attribute.input_type == TEXT || $attribute.input_type == RICH_TEXT)) && 
                !empty($category.attributes[$attribute.code])
            }
                {assign var = attribute_value value = $category.attributes[$attribute.code]|json_decode:1}                
                {$attribute.value = "{if !empty($attribute_value[$lang])}{$attribute_value[$lang]}{/if}"}
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