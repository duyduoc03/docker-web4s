{if empty($collection_info.fields)}
    {$collection_info.fields = [
        'name' => {__d('admin', 'truong_moi')}
    ]}
{/if}

{assign var = list_type value = $this->ExtendCollectionAdmin->getListTypeInput()}

<div nh-wrap="fields">                                                                
    {foreach $collection_info.fields key = index item = field}
        {assign var = input_type value = "{if !empty($field.input_type)}{$field.input_type}{/if}"}

        <div nh-item="field" class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item">
            <div class="kt-portlet__head p-5 ui-sortable-handle mh-40">
                <div class="kt-portlet__head-label ml-5">
                    <h3 class="kt-portlet__head-title">
                        {if !empty($field.name)}
                            {$field.name}
                        {else}
                            {__d('admin', 'truong_moi')}
                        {/if}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <span nh-btn="delete" class="btn btn-sm btn-icon btn-danger btn-icon-md m-0">
                            <i class="la la-trash-o"></i>
                        </span>
                        <span nh-btn="toggle" class="btn btn-sm btn-icon btn-info btn-icon-md m-0">
                            <i class="la la-angle-down"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body p-10">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_truong')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-columns"></i>
                                    </span>
                                </div>
                                <input type="text" name="fields[{$index}][name]" data-name="name" value="{if !empty($field.name)}{$field.name}{/if}" class="form-control form-control-sm name-field" autocomplete="off">
                            </div>                            
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'bat_buoc_nhap')}
                            </label>

                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="fields[{$index}][required]" data-name="required" value="1" {if !empty($field.required)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio mr-20">
                                    <input type="radio" name="fields[{$index}][required]" data-name="required" value="0" {if empty($field.required)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>                                
                            </div>
                        </div>
                    </div>

                    <div nh-wrap="item-view" class="col-xl-3 {if $input_type == {RICH_TEXT}}d-none{/if}">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'hien_thi_o_danh_sach')}
                            </label>

                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="fields[{$index}][view]" data-name="view" value="1" {if !empty($field.view)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio mr-20">
                                    <input type="radio" name="fields[{$index}][view]" data-name="view" value="0" {if empty($field.view)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>                                
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_truong')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input type="text" name="fields[{$index}][code]" data-name="code" value="{if !empty($field.code)}{$field.code}{/if}" class="form-control form-control-sm {if !empty($field.code)}disabled{/if}" {if !empty($field.code)}readonly{/if} autocomplete="off">
                            </div> 
                            
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="form-group">
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
                            {$this->Form->select("fields[{$index}][input_type]", $list_type, $select_config)}
                        </div>
                    </div>

                    <div nh-wrap="item-multiple-language" class="col-xl-3 {if $input_type != {TEXT} && $input_type != {RICH_TEXT}}d-none{/if}">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'da_ngon_ngu')}
                            </label>
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="fields[{$index}][multiple_language]" data-name="multiple_language" value="1" {if !empty($field.multiple_language)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio mr-20">
                                    <input type="radio" name="fields[{$index}][multiple_language]" data-name="multiple_language" value="0" {if empty($field.multiple_language)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>                                
                            </div>                       
                        </div>
                    </div>
                </div>

                <div nh-wrap="item-option" class="row {if $input_type != 'single_select' && $input_type != 'multiple_select'}d-none{/if}">
                    <div class="col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tuy_chon')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-list-ul"></i>
                                    </span>
                                </div>
                                <textarea type="text" name="fields[{$index}][options]" data-name="options" class="form-control form-control-sm tagify-input">{if !empty($field.options)}{htmlentities($field.options|array_values|@json_encode)}{/if}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>
