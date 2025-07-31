{if empty($wheel_fortune.options)}
    {$wheel_fortune.options[] = [
        'name' => {__d('admin', 'them_giai_thuong')},
        'type_award' => 'nothing'
    ]}
{/if}

{assign var = list_language value = $this->LanguageAdmin->getList()}
{assign var = list_type_award value = $this->ListConstantAdmin->typeAward()}


<div class="list-option" id="wrap-wheel-option">
    {foreach $wheel_fortune.options key = k_index item = option}

        {assign var = type_award value = "{if !empty($option.type_award)}{$option.type_award}{else}nothing{/if}"}

        <div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item {if !$option@first}kt-portlet--collapse1{/if}">
            <div class="kt-portlet__head p-5">
                <div class="kt-portlet__head-label ml-5">
                    <h3 class="kt-portlet__head-title">
                        {assign var = first_lang value = $list_language|@key}
                        {assign var = key_first_name value = "name_{$first_lang}"}
                        
                        {if !empty($option.content.$key_first_name)}
                            {$option.content.$key_first_name}
                        {else}
                            {__d('admin', 'giai_thuong')}
                        {/if}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">                        
                        <span class="btn btn-sm btn-icon btn-secondary btn-icon-md m-0 btn-delete-item">
                            <i class="la la-trash-o"></i>
                        </span>

                        <span class="btn btn-sm btn-icon btn-info btn-icon-md m-0 btn-toggle-item">
                            <i class="la la-angle-down"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body p-10" style="{if !$option@first}display: block;{/if}">
                <div class="row">
                    

                    {if !empty($list_language)}
                        {foreach from = $list_language item = language key = k_lang name = title_item}
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'giai_thuong')}
                                        ({$language})
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <div class="list-flags">
                                                    <i class="fa fa-align-left"></i>
                                                </div>
                                            </span>
                                        </div>

                                        {assign var = key_name value = "name_{$k_lang}"}
                                        <input name="options[{$k_index}][content][name_{$k_lang}]" data-name="name_{$k_lang}" data-content="1" value="{if !empty($option.content.$key_name)}{$option.content.$key_name}{/if}" class="form-control form-control-sm required {if $smarty.foreach.title_item.first}item-name{/if}" type="text" data-msg="{__d('admin', 'vui_long_nhap_thong_tin')}">

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <div class="list-flags">
                                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                                </div>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group" prize-value>
                                    <label>
                                        {__d('admin', 'gia_tri_giai_thuong')} ({$language})
                                    </label>

                                    {assign var = key_value value = "value_{$k_lang}"}
                                    {if !empty($list_type_award)}
                                        {foreach from = $list_type_award key = code item = type}
                                            <textarea {if $type_award == $code}name="options[{$k_index}][content][{$key_value}]"  data-content="1" data-name="{$key_value}" {/if}  data-type="{$code}" class="form-control form-control-sm required {if $type_award !== $code}d-none{/if}" data-msg="{__d('admin', 'vui_long_nhap_thong_tin')}" rows="2" {if $type_award === "nothing"}disabled{/if}>{if !empty($option.content.$key_value) && $type_award == $code}{$option.content.$key_value}{/if}</textarea>
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                    {/if}

                    <div class="col-lg-2 col-12">
                        <div class="form-group ">
                            <label>
                                {__d('admin', 'loai_giai_thuong')}
                            </label>

                            {assign var = select_config value = [
                                'empty' => "-- {__d('admin', 'loai_giai_thuong')} --", 
                                'default' => $type_award, 
                                'class' => "form-control form-control-sm kt-selectpicker", 
                                'data-size' => 7, 
                                'data-name' => 'type_award'
                            ]}
                            {$this->Form->select("options[{$k_index}][type_award]", $list_type_award, $select_config)}
                        </div>
                    </div>

                    <div class="col-lg-2 col-12">
                        <div class="form-group">
                            <label for="wheel-demo">{__d('admin', 'mau_sac')}</label>
                            <input type="text" name="options[{$k_index}][color]" value="{if !empty($option.color)}{$option.color}{/if}" class="form-control form-control-sm demo required" data-control="wheel" data-name="color" data-msg="{__d('admin', 'vui_long_nhap_thong_tin')}">
                        </div>
                    </div>

                    <div class="col-lg-2 col-12">
                        <div class="form-group ">
                            <label>
                                {__d('admin', 'co_hoi')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="flaticon2-percentage"></i>
                                    </span>
                                </div>
                                <input name="options[{$k_index}][percent_winning]" value="{if !empty($option.percent_winning)}{$option.percent_winning}{else}0{/if}" type="text" max="100" class="form-control form-control-sm number-input required" data-name="percent_winning" data-msg="{__d('admin', 'vui_long_nhap_thong_tin')}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'gioi_han_giai_thuong')}
                            </label>
                            <input name="options[{$k_index}][limit_prize]" value="{if !empty($option.limit_prize)}{$option.limit_prize}{/if}" type="text" max="100" class="form-control form-control-sm number-input" data-name="limit_prize" {if empty($wheel_fortune.check_limit)}disabled{/if}>
                        </div>
                    </div>

                    <input name="options[{$k_index}][id]" data-name="id" value="{if !empty($option.id)}{$option.id}{/if}" class="form-control form-control-sm" type="hidden">
                </div>
            </div>
        </div>
    {/foreach}
</div>

        