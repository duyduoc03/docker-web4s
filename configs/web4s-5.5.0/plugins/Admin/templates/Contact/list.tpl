<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar align-self-center">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <div class="dropdown dropdown-inline">
                        <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> {__d('admin', 'xuat_excel')}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 250px;">
                            <ul class="kt-nav">
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="current">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_trang_hien_tai')}</span>
                                    </a>
                                </li>
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="all">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_toan_bo_cac_trang')}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body">
            <div class="kt-form" responsive-tab>
                {assign var = list_form value = $this->ContactFormAdmin->getListForm()}
                {if !empty($list_form)}
                    {foreach from = $list_form key = key item = name}
                        <li class="btn btn-sm btn-secondary {if !empty($form_id) && $form_id == $key}btn-primary{/if}" change-form data-id="{if !empty($key)}{$key}{/if}">
                            {if !empty($name)}{$name}{/if}
                        </li>
                    {/foreach}
                {else}
                    <a class="btn btn-sm btn-primary" href="{ADMIN_PATH}contact/form/add">
                        {__d('admin', 'them_form_lien_he')}
                    </a>
                {/if}
            </div>
        </div>
    </div>

    {if !empty($list_form)}
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__body">
                <div class="nh-search-advanced">
                    <div class="kt-form">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-input-icon kt-input-icon--left">
                                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')} ..." autocomplete="off">
                                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                                <span><i class="la la-search"></i></span>
                                            </span>
                                        </div>
                                    </div>  

                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="input-group">
                                            <input id="tracking_source" name="tracking_source" type="text" class="form-control form-control-sm tagify-input rounded" placeholder="{__d('admin', 'nguon')} ..." autocomplete="off">
                                        </div>
                                    </div>  

                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-form__group">
                                            <select name="status" id="nh_status" class="form-control form-control-sm kt-selectpicker">
                                                <option value="" selected="selected">
                                                    -- {__d('admin', 'trang_thai')} --
                                                </option>
                                                <option value="1">{__d('admin', 'da_doc')}</option>
                                                <option value="2">{__d('admin', 'chua_doc')}</option>
                                            </select>
                                        </div>
                                    </div>  

                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-form__group">
                                            <div class="input-daterange input-group">
                                                <input id="create_from" type="text" class="form-control form-control-sm kt_datepicker" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-ellipsis-h"></i>
                                                    </span>
                                                </div>
                                                <input id="create_to" type="text" class="form-control form-control-sm kt_datepicker" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                                            </div>
                                        </div>    
                                    </div>                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <div class="dropdown mr-10">
                                            <button type="button" class="btn btn-sm btn-label-primary dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                                {__d('admin', 'thay_doi_trang_thai')}
                                            </button>
                                            <div class="dropdown-menu">
                                                {foreach from = $this->ListConstantAdmin->listStatusContact() key = k_status item = status}
                                                    <a class="dropdown-item nh-change-status-all" data-status="{$k_status}" href="javascript:;">
                                                        {$status}
                                                    </a>
                                                {/foreach}
                                            </div>
                                        </div>
                                      
                                        <button class="btn btn-sm btn-label-danger nh-delete-all mobile-mb-5" type="button">
                                            <i class="la la-trash-o"></i>
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
    {/if}
</div>

<div id="modal-detail-contact" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{__d('admin', 'thong_tin_lien_he')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var fields = {if !empty($fields)}{$fields}{/if};    
</script>