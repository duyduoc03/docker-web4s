<div class="kt-portlet__body p-0">
    <div class="kt-scroll" data-scroll="true" data-height="600" >
        {if !empty($options)}
            {foreach from = $options item = item_options}
                {if !empty($item_options.name)}
                    <div class="kt-separator kt-separator--border-dashed mt-0 mb-5 pb-10 pt-10 h-auto">
                        {$item_options.name}
                    </div>
                {/if}
                
                {if !empty($item_options.options)}
                    <div class="row" nh-list-option>
                        {foreach from = $item_options.options item = option}
                            {assign var = option_id value = ""}
                            {if !empty($option.id)}
                                {assign var = option_id value = $option.id}
                            {/if}

                            {assign var = attribute_id value = ""}
                            {if !empty($item_options.attribute_id)}
                                {assign var = attribute_id value = $item_options.attribute_id}
                            {/if}

                            {assign var = checked value = ""}
                            {if !empty($option_id) && !empty($options_selected[$attribute_id]) && in_array($option_id, $options_selected[$attribute_id])}
                                {assign var = checked value = "checked"}
                            {/if}

                            <div class="col-sm-6 col-12"> 
                                {if !empty($option.name)}
                                    <div class="kt-separator kt-separator--dashed mb-0 mt-0 pb-10 pt-10 h-auto" >
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--info kt-widget4__item mb-0">
                                            <input type="checkbox" value="{if !empty($option_id)}{$option_id}{/if}" name="options[{$attribute_id}][]" {$checked}> {$option.name}
                                            <span></span>
                                        </label>
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
                {/if}
            {/foreach}
        {/if}
    </div> 
</div>
