{assign var = url_list value = "{ADMIN_PATH}/author"}
{assign var = url_add value = "{ADMIN_PATH}/author/add"}
{assign var = url_edit value = "{ADMIN_PATH}/author/update"}
{assign var = url_edit value = "{ADMIN_PATH}/author/update"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {if !empty($url_edit) || !empty($url_add)}
                <div class="btn-group">
                    {if empty($id)}
                        <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-plus"></i>
                            {__d('admin', 'them_moi')} (F1)
                        </button>
                    {else}
                         <button id="btn-save" after-save="keep-here" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-edit"></i>
                            {__d('admin', 'cap_nhat')} (F1)
                        </button>
                    {/if}
                    
                    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="kt-nav p-0">
                            {if !empty($url_edit)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_edit}" data-update="1" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-writing"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_tiep_tuc')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_add)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_add}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-medical-records"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_them_moi')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_list)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_list}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-hourglass-1"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_quay_lai')}
                                        </span>
                                    </span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            {/if}
            
            {$this->element('Admin.page/language')}
            
        </div>
    </div>
</div>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/author/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <input type="hidden" name="article_id" value="{if !empty($id)}{$id}{/if}">
        <input type="hidden" name="draft" value="">

        <div class="clearfix" style="min-height: 900px;">
            <div class="kt-portlet kt-portlet--tabs">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-toolbar">
                        <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab-basic-info" role="tab">
                                    {__d('admin', 'thong_tin_co_ban')}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-description" role="tab">
                                    {__d('admin', 'mo_ta')}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-social" role="tab">
                                    {__d('admin', 'mang_xa_hoi')}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-seo" role="tab">
                                    SEO
                                </a>
                            </li>
                            {if !empty($id)}
                                <li class="nav-item">
                                    <a nh-btn="show-log-tab" data-toggle="tab" href="#tab-log" role="tab" class="nav-link">
                                        {__d('admin', 'lich_su_cap_nhat')} 
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-basic-info" role="tabpanel">
                            {$this->element('../Author/element_basic_info')}
                        </div>

                        <div class="tab-pane" id="tab-description" role="tabpanel">
                            {$this->element('../Author/element_description')}
                        </div>
                        <div class="tab-pane" id="tab-seo" role="tabpanel">
                            {$this->element('../Author/element_seo')}
                        </div>
                        <div class="tab-pane" id="tab-social" role="tabpanel">
                            {$this->element('../Author/element_social')}
                        </div>

                        <div id="tab-log" nh-tab="log-tab" record-id="{if !empty($id)}{$id}{/if}" sub-type="{AUTHOR}" class="tab-pane" role="tabpanel"></div>
                    </div>  
                </div>
            </div>
        </div>
    </form>
</div>

{$this->element('page/modal_log')}