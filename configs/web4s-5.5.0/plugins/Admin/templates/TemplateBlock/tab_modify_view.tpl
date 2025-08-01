<div id="tab-modify-view" class="tab-pane" role="tabpanel">
    <form id="modify-view-form" action="{ADMIN_PATH}/template/block/save/file-view{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group validated">
                    <label>
                        {__d('admin', 'giao_dien_block')}
                    </label>
                    {$this->Form->select('view_file', $files, ['id' => 'view-file', 'empty' => null, 'default' => "{if !empty($block_info.view)}{$block_info.view}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker is-invalid'])}
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <label class="h-15px"></label>
                    <div class="clearfix">
                        <span id="btn-add-view" class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i>
                            {__d('admin', 'them_giao_dien_moi')}
                        </span>
                        
                        <span id="btn-delete-view" class="btn btn-sm btn-secondary">
                            <i class="fa fa-trash-alt"></i>
                            {__d('admin', 'xoa_giao_dien')}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>

        <div class="form-group">
            {if $supper_admin}
                <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
                    <i class="fa fa-images"></i>
                    {__d('admin', 'chon_anh_giao_dien')}
                </span>
                <input id="image_template" type="hidden" value="">
            {/if}
            
            {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

            <span btn-select-media-block="cdn" action="copy" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand">
                <i class="fa fa-photo-video"></i>
                {__d('admin', 'chon_anh_tu_cdn')}
            </span>                    

            <span nh-btn="view-full-screen-editor" class="btn btn-sm btn-secondary float-right">
                <i class="fa fa-expand"></i>
                {__d('admin', 'toan_man_hinh')}
            </span>

            <span nh-btn="view-history-change-file" data-path="{if !empty($path_first_file)}{$path_first_file}{/if}" class="btn btn-sm btn-secondary mr-5 float-right">
                <i class="fa fa-file-alt"></i>
                {__d('admin', 'lich_su_thay_doi_cua_tep')}
            </span>
        </div>

        <div id="editor-modify-view" class="nh-editor"></div>
        <input id="input-view-file-content" name="view_file_content" value="{if !empty($file_first_content)}{htmlentities($file_first_content)}{/if}" type="hidden">

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>

        <div class="form-group mb-0">
            <span class="btn btn-sm btn-brand btn-save">
                <i class="fa fa-check"></i>
                {__d('admin', 'luu_giao_dien')}
            </span>
        </div>
    </form>
</div>