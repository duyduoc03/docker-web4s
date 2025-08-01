<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/translate-locale-process" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'dich_file_locale')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="row align-items-center">
                    {if !empty($languages)}
                        <div class="col-3">
                            <div class="kt-form__group mb-3">
                                <label>
                                    {__d('admin', 'ngon_ngu')}
                                </label>
                                <select name="lang to" class="form-control form-control-sm kt-selectpicker">
                                    <option value="" selected="selected">
                                        -- {__d('admin', 'chon_ngon_ngu')} --
                                    </option>
                                    {foreach from = $languages key = lang item = language}
                                        <option value="{$lang}">{$language}</option>
                                    {/foreach}   
                                </select>
                            </div>
                        </div>
                    {/if}
                        <span class="mt-3"><i class="fa fa-exchange-alt"></i></span>
                    {if !empty($languages)}
                        <div class="col-3">
                            <div class="kt-form__group mb-3">
                                <label>
                                    {__d('admin', 'ngon_ngu_dich')}
                                </label>
                                <select name="lang from" class="form-control form-control-sm kt-selectpicker">
                                    <option value="" selected="selected">
                                        -- {__d('admin', 'chon_ngon_ngu')} --
                                    </option>
                                    {foreach from = $languages key = lang item = language}
                                        <option value="{$lang}">{$language}</option>
                                    {/foreach}   
                                </select>
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="form-group">
                    <label>
                        {__d('admin', 'kieu_tep_ngon_ngu')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="type"  value="po"> 
                                .po
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="type" value="js"> 
                                .js
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="row align-items-center mb-3">
                    <div class=" col-md-3">
                        <label class="form-label">{__d('admin', 'tep_da_dich_san')}</label>
                        <input class="form-control form-control-sm h-100" id="fileInput_to" type="file" name="file_to">
                    </div>
                        <span class="mt-3"><i class="fa fa-exchange-alt"></i></span>
                    <div class=" col-md-3">
                        <label class="form-label">{__d('admin', 'tep_can_dich')}</label>
                        <input class="form-control form-control-sm h-100" id="fileInput_from" type="file" name="file_from">
                    </div>
                </div>
                
                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'dich')}
                    </button>
                    <a href="javscript:;" class="ml-3 d-none" nh-dowload-file target="_blank" download> 
                        <i class="fa fa-download"></i> {__d('admin', 'tai_tep')}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>