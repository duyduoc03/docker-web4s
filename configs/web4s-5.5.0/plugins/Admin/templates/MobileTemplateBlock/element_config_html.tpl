<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-database mr-5"></i>
                {__d('admin', 'cau_hinh_du_lieu')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form id="data-extend-form" action="{ADMIN_PATH}/mobile-app/block/save-data-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="form-group">
                {if $supper_admin}
                    <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
                        <i class="fa fa-images"></i>
                        {__d('admin', 'chon_anh_giao_dien')}
                    </span>
                    <input id="image_template" type="hidden" value="">
                {/if}
                
                {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

                <span data-src="{$url_select_image}" btn-select-media-block="cdn" action="copy" data-type="iframe" class="btn btn-sm btn-brand">
                    <i class="fa fa-photo-video"></i>
                    {__d('admin', 'chon_anh_tu_cdn')}
                </span>


                <span nh-btn="show-modal-config-action" type_result="link" class="btn btn-sm btn-secondary  w-auto">
                    <i class="fa fa-external-link-alt"></i>
                    {__d('admin', 'lay_action_mobile')}
                </span>
                          

                <span nh-btn="full-screen-html-editor" class="btn btn-sm btn-secondary float-right">
                    <i class="fa fa-expand"></i>
                    {__d('admin', 'toan_man_hinh')}
                </span>
            </div>


            <div class="form-group">
                <div id="editor-html" class="nh-editor"></div>
                <input id="html-content" name="config[html_content]" value="{if !empty($config_data.config.html_content)}{htmlentities($config_data.config.html_content)}{/if}" type="hidden">
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

