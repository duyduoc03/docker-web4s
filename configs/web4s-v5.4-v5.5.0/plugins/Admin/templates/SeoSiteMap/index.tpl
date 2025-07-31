<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_sitemap')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="sitemap-form" action="{ADMIN_PATH}/setting/save/sitemap" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ap_dung_sitemap')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="apply_sitemap" value="0" {if empty($sitemap.apply_sitemap)}checked{/if}> 
                                    {__d('admin', 'sitemap_tu_dong')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="apply_sitemap" value="1" {if !empty($sitemap.apply_sitemap)}checked{/if}> 
                                    {__d('admin', 'sitemap_thu_cong')}
                                    <span></span>
                                </label>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div id="wrap-manual-sitemap" class="collapse {if !empty($sitemap.apply_sitemap)}show{/if} col-12 col-lg-3 px-0">
                    <div class="kt-portlet__body p-0 mb-30">
                        {assign var = dropzone_params value = [
                            'id_dropzone' => 'sitemap',
                            'title_dropzone' => "{if !empty($sitemap_manual)}{__d('admin', 'cap_nhat_file_sitemap')}{else}{__d('admin', 'tai_file_sitemap')}{/if}",
                            'slogan_dropzone' => "{__d('admin', 'chi_ho_tro_dinh_dang_file')} .xml"
                        ]}
                        {$this->element('Admin.page/dropzone_upload', ['dropzone_params' => $dropzone_params])}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button id="btn-save-sitemap" type="button" class="btn btn-brand btn-sm">
                            {__d('admin', 'luu_thong_tin')}
                        </button>

                        <a href="/sitemap.xml" target="_blank" title="{__d('admin', 'xem_sitemap')}" class="btn btn-success btn-sm">
                            <i class="fa fa-sitemap"></i>
                            {__d('admin', 'xem_sitemap')}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>