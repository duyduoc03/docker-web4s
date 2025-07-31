<div class="kt-portlet__body p-0">
    <div class="kt-scroll" data-scroll="true" data-height="600" nh-list-attributes-{if !empty($type_attribute)}{$type_attribute}{/if}>
        {if !empty($attributes)}
            <div class="row">
                {foreach from = $attributes key = key item = item}
                    {assign var = attribute_id value = ""}
                    {if !empty($item.id)}
                        {assign var = attribute_id value = $item.id}
                    {/if}

                    {assign var = checked value = ""}
                    {if !empty($attribute_id) && !empty($attributes_selected) && in_array($attribute_id, $attributes_selected)}
                        {assign var = checked value = "checked"}
                    {/if}

                    {assign var = input_type value = ""}
                    {if (!empty($item.input_type)) && ($item.input_type == 'multiple_select' || $item.input_type == 'single_select' || $item.input_type == 'special_select_item')}
                        {assign var = input_type value = 'nh-attribute-select="option"'}
                    {/if}

                    <div class="col-sm-6 col-12">
                        {if !empty($item.name)}
                            <div class="kt-separator kt-separator--dashed mb-0 mt-0 pb-10 pt-10 h-auto ">
                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--info kt-widget4__item mb-0">
                                    <input type="checkbox" value="{if !empty($attribute_id)}{$attribute_id}{/if}" {$input_type}  nh-category ="{if !empty($category_id)}{$category_id}{/if}" nh-type="{if !empty($item.attribute_type)}{$item.attribute_type}{/if}" name="attributes[]" {$checked}> {$item.name}
                                    <span></span>
                                </label>
                            </div>
                        {/if}
                    </div>
                {/foreach}
            </div>
        {/if}
    </div> 
</div>
