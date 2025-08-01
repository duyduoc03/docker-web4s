{$this->element('../Product/element_subheader')}

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/product/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">  

        <input type="hidden" name="draft" value="">

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
                                <a class="nav-link" data-toggle="tab" nh-scroll-top href="#tab-price" role="tab">
                                    {__d('admin', 'gia_va_phien_ban_san_pham')}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" nh-scroll-top href="#tab-description" role="tab">
                                    {__d('admin', 'mo_ta_san_pham')}
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
                                    <a nh-btn="show-comment-tab" class="nav-link" nh-scroll-top data-toggle="tab" href="#tab-comment" role="tab" tab-comment>
                                        {__d('admin', 'binh_luan_danh_gia')}
                                    </a>
                                </li>
                            
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
                                {$this->element('../Product/element_basic_info_detail')}

                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-20 mb-20"></div>
                            {/if}
                            {$this->element('../Product/element_basic_info')}
                        </div>

                        <div class="tab-pane" id="tab-price" role="tabpanel">
                            {$this->element('../Product/element_price')}
                        </div>

                        <div class="tab-pane" id="tab-description" role="tabpanel">
                            {$this->element('../Product/element_description')}
                        </div>

                        <div class="tab-pane" id="tab-attributes" role="tabpanel">
                            <div id="attributes-product">
                                {$this->element('../Product/element_attributes')}
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-seo" role="tabpanel">
                            {$this->element('../Product/element_seo')}
                        </div>

                        <div class="tab-pane" id="tab-comment" role="tabpanel">
                            {$this->element('../Product/element_comment')}
                        </div>

                        <div id="tab-log" nh-tab="log-tab" record-id="{if !empty($id)}{$id}{/if}" sub-type="{PRODUCT}" class="tab-pane" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="product_id" value="{if !empty($id)}{$id}{/if}">
    </form>
</div>

{$this->element('page/modal_log')}
{$this->element('page/modal_filter_comment')}

<div id="modal-comment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thong_tin_binh_luan')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>