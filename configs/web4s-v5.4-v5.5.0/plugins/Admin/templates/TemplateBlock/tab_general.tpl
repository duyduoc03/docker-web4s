<div id="tab-general" class="tab-pane active" role="tabpanel">
    <form id="general-config-form" action="{ADMIN_PATH}/template/block/save/general-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <label>
                        Style Class
                    </label>
                    <input name="config[class]" value="{if !empty($config.class)}{$config.class}{/if}" class="form-control form-control-sm" type="text">
                    <span class="form-text text-muted">
                        {__d('admin', 'nhung_class_cau_hinh_se_duoc_dat_o_the_bao_ngoai_cua_block')}
                    </span>
                </div>
            </div>

            {if !empty($files_view) && $type != {HTML}}
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'giao_dien_block')}
                        </label>
                        {$this->Form->select('view', $files_view, ['empty' => null, 'default' => "{if !empty($block_info.view)}{$block_info.view}{else}view.tpl{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
            {/if}

            {if $type == {HTML}}
                <input type="hidden" name="view" value="{$code}.tpl">
            {/if}

            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label class="kt-font-danger">
                        {__d('admin', 'su_dung_cache')} *
                    </label>
                    
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="config[cache]" value="0" {if empty($config.cache)}checked{/if}> 
                                {__d('admin', 'khong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="config[cache]" value="1" {if !empty($config.cache) || !isset($config.cache)}checked{/if}> 
                                {__d('admin', 'co')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div id="wrap-config-type-load" class="clearfix d-none">
            {$this->element('../TemplateBlock/config_type_load', ['type_load' => "{if !empty($config.type_load)}{$config.type_load}{else}{NORMAL}{/if}", 'config' => $config])}
        </div>

        {if !empty($type)}
            {$this->element("../TemplateBlock/element_config_{$type}", ['config' => $config])}
        {/if}

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

        <div class="form-group mb-0">
            <div class="btn-group">
                <span class="btn btn-sm btn-brand btn-save">
                    {__d('admin', 'luu_cau_hinh')}
                </span>
            </div>
        </div>
    </form>
</div>