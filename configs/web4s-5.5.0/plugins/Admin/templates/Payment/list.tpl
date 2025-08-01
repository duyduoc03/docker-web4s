<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div> 

        <div class="kt-subheader__toolbar">
            <div class="btn-group">
                <button data-link="" type="button" class="btn btn-sm btn-brand">
                    <i class="fa fa-file-excel"></i>
                    {__d('admin', 'excel')}
                </button>
                
                <button type="button" class="btn btn-brand btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:;" nh-export="current">
                        <i class="fa fa-file-excel"></i>
                        {__d('admin', 'xuat_excel_trang_hien_tai')}
                    </a>

                    <a class="dropdown-item" href="javascript:;" nh-export="all">
                        <i class="fa fa-file-excel"></i>
                        {__d('admin', 'xuat_excel_toan_bo_cac_trang')}
                    </a>
                </div>
            </div>
        </div>       
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            {$this->element('../Payment/search_advanced')}

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
                <div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mt-0 mb-20"></div>
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
                                    <button class="btn btn-sm btn-label-success nh-delete-all mobile-mb-5 mr-10 nh-change-status-all" data-status="1" type="button">
                                        <i class="fa fa-check"></i>
                                        {__d('admin', 'xac_nhan_thanh_cong')}
                                    </button>  

                                    <button class="btn btn-sm btn-label-dark nh-delete-all mobile-mb-5 nh-change-status-all" data-status="0" type="button">
                                        <i class="la la-trash-o"></i>
                                        {__d('admin', 'huy_giao_dich')}
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

{$this->element('../Payment/modal_quick_view')}

{$this->element('Admin.page/popover_quick_change')}