{assign var = url_list value = "{ADMIN_PATH}/category/{$type}"}
{assign var = url_add value = "{ADMIN_PATH}/category/{$type}/add"}
{assign var = url_edit value = "{ADMIN_PATH}/category/{$type}/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit,
    'show_lang' => true
])}

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/category/{$type}/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="clearfix" style="min-height: 900px;">
            <div class="kt-portlet kt-portlet--tabs">
                <div class="kt-portlet__head kt-portlet__head-sticky sticky" data-sticky="true" data-margin-top="118px" data-sticky-for="1023" data-sticky-class="sticky" style="">
                    <div class="kt-portlet__head-toolbar">
                        <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" nh-scroll-top href="#tab-basic-info" role="tab">
                                    {__d('admin', 'thong_tin_co_ban')}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" nh-scroll-top href="#tab-description" role="tab">
                                    {__d('admin', 'mo_ta')}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" nh-scroll-top href="#tab-attributes" role="tab">
                                    {__d('admin', 'thuoc_tinh_mo_rong')}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" nh-scroll-top href="#tab-seo" role="tab">
                                    SEO
                                </a>
                            </li>
                            {if !empty($id)}
                                <li class="nav-item">
                                    <a nh-btn="show-log-tab" data-toggle="tab" nh-scroll-top href="#tab-log" role="tab" class="nav-link">
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
                            {if !empty($id)}
                                {$this->element('../Category/element_basic_info_detail')}

                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-20 mb-20"></div>
                            {/if}

                            {$this->element('../Category/element_basic_info')}
                        </div>

                        <div class="tab-pane" id="tab-description" role="tabpanel">
                            {$this->element('../Category/element_description')}
                        </div>

                        <div class="tab-pane" id="tab-attributes" role="tabpanel">
                            {$this->element('../Category/element_attributes')}
                        </div>

                        <div class="tab-pane" id="tab-seo" role="tabpanel">
                            {$this->element('../Category/element_seo')}
                        </div>
                        <div id="tab-log" nh-tab="log-tab" record-id="{if !empty($id)}{$id}{/if}" sub-type="{CATEGORY}" class="tab-pane" role="tabpanel"></div>
                    </div>  
                </div>
            </div>
        </div>
    </form>
</div>

{$this->element('page/modal_log')}