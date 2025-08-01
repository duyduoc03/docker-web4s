{assign var = list_type value = $this->ContactFormAdmin->getListTypeInput()}

{assign var = input_type value = "{if !empty($field.input_type)}{$field.input_type}{/if}"}
{assign var = options value = "{if !empty($field.options)}{json_encode($field.options)}{/if}"}
<div data-repeater-item class="row wrap-list">
    <div class="col-xl-3 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'ma_truong')}
                <span class="kt-font-danger">*</span>
            </label>
            <input name="code" value="{if !empty($field.code)}{$field.code}{/if}" class="form-control form-control-sm required" type="text" message-required="{__d('admin', 'vui_long_nhap_thong_tin')}">
        </div>
    </div>

    <div class="col-xl-3 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'ten_truong')}
                <span class="kt-font-danger">*</span>
            </label>
            <input name="label" value="{if !empty($field.label)}{$field.label}{/if}" class="form-control form-control-sm required" type="text" message-required="{__d('admin', 'vui_long_nhap_thong_tin')}">
        </div>
    </div>

    <div class="col-xl-2 col-lg-2">
        <div class="form-group">
            <label>
                {__d('admin', 'hien_thi_o_danh_sach')}
            </label>

            <div class="kt-radio-inline mt-5">
                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                    <input type="radio" name="fields[][view]" data-name="view" value="1" {if !empty($field.view)}checked{/if}> 
                        {__d('admin', 'co')}
                    <span></span>
                </label>

                <label class="kt-radio mr-20">
                    <input type="radio" name="fields[][view]" data-name="view" value="0" {if empty($field.view)}checked{/if}> 
                        {__d('admin', 'khong')}
                    <span></span>
                </label>                                
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="form-group">
            <div class="wrap-item">
                <label>
                    {__d('admin', 'loai_input')}
                    <span class="kt-font-danger">*</span>
                </label>       

                {assign var = select_config value = [
                    'empty' => "-- {__d('admin', 'loai_input')} --", 
                    'default' => $input_type, 
                    'class' => "form-control form-control-sm kt-selectpicker disabled", 
                    'data-size' => 7, 
                    'data-name' => 'input_type'
                ]}

                {$this->Form->select("fields[input_type]", $list_type, $select_config)}
                
                <div nh-wrap="item-option" class="row {if $input_type != 'single_select' && $input_type != 'multiple_select'}d-none{/if}">
                    <div class="col-12">
                        <div class="form-group mt-10">
                            <label>
                                {__d('admin', 'tuy_chon')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-list-ul"></i>
                                    </span>
                                </div>

                                <textarea type="text" name="fields[][options]" data-name="options" class="form-control form-control-sm tagify-input">
                                     {if !empty($options)}{$options}{/if}
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
        </div>            
    </div>

    <div class="col-xl-1 col-lg-1">
        <div class="form-group">
            <label></label>
            <div class="clearfix"> 
                <span data-repeater-delete class="btn btn-sm btn-danger mt-5">
                    <i class="la la-trash-o"></i>
                    {__d('admin', 'xoa')}
                </span>
            </div>
        </div>
    </div>
</div>