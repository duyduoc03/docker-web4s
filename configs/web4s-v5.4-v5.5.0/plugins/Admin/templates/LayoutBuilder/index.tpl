<div class="nh-admin-bar">
    <div class="row">
        <div class="col-12 col-md-4">
            <a href="/" target="_blank">
                <img alt="Logo" src="/admin/assets/media/logos/logo4s-02.svg" style="height: 25px;">
                <i class="text-muted">
                    Version {ADMIN_VERSION_UPDATE}
                </i>
            </a>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-center">
                <span nh-change-device="desktop" class="btn text-white">
                    <i class="fas fa-desktop"></i>
                    Desktop
                </span>

                <span nh-change-device="mobile" class="btn text-white">
                    <i class="fas fa-mobile-alt"></i>
                    Mobile
                </span>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="text-right">
                <span nh-prev="url" class="btn px-3 text-white">
                    <i class="fas fa-arrow-left fs-1 p-0"></i>
                    Back
                </span>

                <span nh-next="url" class="btn px-3 text-white">
                    Next
                    <i class="fas fa-arrow-right fs-1"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="wrap-iframe">
    <div id="demo-webite">
        <iframe id="iframe-website" src="{$this->UtilitiesAdmin->getUrlWebsite()}?nh-mode=layout-builder&nh-device=desktop"></iframe>
    </div>
</div>

<div id="layout-builder-block-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl kt-margin-t-50" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_block')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>

<div id="add-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog kt-margin-t-65" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_giao_dien_block')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="add-view-form" action="{ADMIN_PATH}/template/block/add-view{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_giao_dien')}
                        </label>
                        <div class="input-group">
                            <input name="name_file" value="" type="text" class="form-control form-control-sm required">
                            <div class="input-group-append">
                                <span class="input-group-text">.tpl</span>
                            </div>
                        </div>
                    </div> 
                </form>                 
            </div>

            <div class="modal-footer">
                <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </span>
                
                <span id="btn-save-view" class="btn btn-sm btn-brand">
                    <i class="fa fa-plus"></i>
                    {__d('admin', 'them_moi')}
                </span>
            </div>
        </div>
    </div>
</div>