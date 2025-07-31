<div class="kt-subheader kt-grid__item" id="kt_subheader">
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

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div id="locales-system" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="list-locales" action="/admin/setting/locales-system-save" method="POST" autocomplete="off">
        <div class="kt-portlet kt-portlet--tabs kt-portlet--tabs-locales">
            <div class="kt-portlet__head align-items-center">
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" nh-tab-element="locale" nh-type-data="system_po" data-toggle="tab" href="#tab-1" role="tab">
                                File Locale
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="block" nh-type-data="block" data-toggle="tab" href="#tab-21" role="tab">
                                Block
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="category" nh-type-data="category_product" data-toggle="tab" href="#tab-2" role="tab">
                                {__d('admin', 'danh_muc')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="product" nh-type-data="product" data-toggle="tab" href="#tab-3" role="tab">
                                {__d('admin', 'san_pham')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="article" nh-type-data="article" data-toggle="tab" href="#tab-4" role="tab">
                                {__d('admin', 'bai_viet')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="brand" nh-type-data="brand" data-toggle="tab" href="#tab-5" role="tab">
                                {__d('admin', 'thuong_hieu')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="extend-collection" nh-type-data="extend_collection" data-toggle="tab" href="#tab-6" role="tab">
                                {__d('admin', 'du_lieu_mo_rong')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" nh-tab-element="attribute" nh-type-data="attribute" data-toggle="tab" href="#tab-7" role="tab">
                                {__d('admin', 'thuoc_tinh_mo_rong')}
                            </a>
                        </li>
                    </ul>
                </div>
                <span class="input-check-all btn btn-sm btn-label-brand btn-bold cursor" nh-btn="data-extend-translate-all" nh-language-default="{$lang_default}">
                    {__d('admin', 'dich_tat_ca_trang_hien_tai')}
                </span>
            </div>

            <div class="kt-portlet__body">
                <div class="tab-content">
                    <input type="hidden" nh-type-load-view="system_po">
                    <div class="tab-pane active" id="tab-1" nh-content-element="locale" role="tabpanel">
                        {$this->element('../SettingLocale/list_locale')}
                        
                    </div>

                    <div class="tab-pane" id="tab-21" nh-content-element="block" role="tabpanel">
                        {$this->element('../SettingLocale/list_block')}
                    </div>

                    <div class="tab-pane" id="tab-2" nh-content-element="category" role="tabpanel">
                        {$this->element('../SettingLocale/list_category')}
                    </div>

                    <div class="tab-pane" id="tab-3" nh-content-element="product" role="tabpanel">
                        {$this->element('../SettingLocale/list_product')}
                    </div>

                    <div class="tab-pane" id="tab-4" nh-content-element="article" role="tabpanel">
                        {$this->element('../SettingLocale/list_article')}
                    </div>

                    <div class="tab-pane" id="tab-5" nh-content-element="brand" role="tabpanel">
                        {$this->element('../SettingLocale/list_brand')}
                    </div>

                    <div class="tab-pane" id="tab-6" nh-content-element="extend-collection" role="tabpanel">
                        {$this->element('../SettingLocale/list_extend_collection')}
                    </div>

                    <div class="tab-pane" id="tab-7" nh-content-element="attribute" role="tabpanel">
                        {$this->element('../settingLocale/list_attribute')}
                    </div>
                </div>  
            </div>
        </div>
    </form>
</div>