{$this->element('../WheelFortune/element_subheader')}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/wheel-fortune/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <input type="hidden" name="wheel_id" value="{if !empty($id)}{$id}{/if}">

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
                                <a class="nav-link" data-toggle="tab" href="#tab-config-email" role="tab">
                                    {__d('admin', 'cau_hinh_gui_thong_tin')}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-config-behavior" role="tab">
                                    {__d('admin', 'cau_hinh_hanh_vi_khach_hang')}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-basic-info" role="tabpanel">
                            {$this->element('../WheelFortune/element_basic_info')}
                        </div>

                        <div class="tab-pane" id="tab-config-email" role="tabpanel">
                            {$this->element('../WheelFortune/element_config_email')}
                        </div>

                        <div class="tab-pane" id="tab-config-behavior" role="tabpanel">
                            {$this->element('../WheelFortune/element_config_behavior')}
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </form>
</div>