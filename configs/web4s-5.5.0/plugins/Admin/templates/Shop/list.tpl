{assign var = permissions value = $this->RoleAdmin->getPermissionAllRouter()}
{assign var = link_shop_add value = "{ADMIN_PATH}/shop/add"}
{assign var = access_shop_add value = "{if !empty($permissions[$link_shop_add])}1{/if}"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'he_thong_cua_hang')}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{$link_shop_add}" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            {$this->element('../Shop/search_advanced')}

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__label kt-form__label-no-wrap">
                                <label class="kt-font-bold kt-font-danger-">
                                    {__d('admin', 'da_chon')}
                                    <span id="nh-selected-number">0</span> :
                                </label>
                            </div>

                            <div class="kt-form__control">
                                <div class="btn-toolbar">
                                    <div class="dropdown mr-10">
                                        <button type="button" class="btn btn-brand btn-sm dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                            {__d('admin', 'trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item nh-change-status-all" data-status="1" href="javascript:;">
                                                {__d('admin', 'hoat_dong')}
                                            </a>
                                            <a class="dropdown-item nh-change-status-all" data-status="0" href="javascript:;">
                                                {__d('admin', 'khong_hoat_dong')}
                                            </a>
                                        </div>
                                    </div>
                                  
                                    <button class="btn btn-sm btn-danger nh-delete-all mobile-mb-5" type="button">
                                        {__d('admin', 'xoa_tat_ca')}
                                    </button>                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>