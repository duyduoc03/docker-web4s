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
    <form id="replace-form" action="{ADMIN_PATH}/setting/replace-content/save" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {if !empty($title_for_layout)}{$title_for_layout}{/if}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label class="mb-15">
                        {__d('admin', 'noi_dung')}
                    </label>
                    <div class="kt-checkbox-list">
                        <label class="kt-checkbox">
                            <input type="checkbox" name="type[]" value="{CATEGORY}"> {__d('admin', 'danh_muc')} 
                            <span></span>
                        </label>
                        <label class="kt-checkbox">
                            <input type="checkbox" name="type[]" value="{PRODUCT}"> {__d('admin', 'san_pham')}
                            <span></span>
                        </label>
                        <label class="kt-checkbox">
                            <input type="checkbox" name="type[]" value="{ARTICLE}"> {__d('admin', 'bai_viet')}
                            <span></span>
                        </label>
                        <label class="kt-checkbox">
                            <input type="checkbox" name="type[]" value="{BRAND}"> {__d('admin', 'thuong_hieu')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tu_khoa_tim_kiem')}
                            </label>

                            <input name="find" value="" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tu_khoa_thay_the')}
                            </label>

                            <input name="replace" value="" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>
                <div class="kt-form__actions">
                    <button type="button" class="btn btn-brand btn-sm btn-save" btn-replace>
                        <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                        {__d('admin', 'cap_nhat')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
