<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/extend-data/{$code}" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>

            {if !empty($id)}
                <span nh-btn="save" class="btn btn-sm btn-primary" shortcut="112">
                    {__d('admin', 'cap_nhat')} (F1)
                </span>
            {else}
                <span nh-btn="save" class="btn btn-sm btn-primary" shortcut="112">
                    {__d('admin', 'them_moi')} (F1)
                </span>
            {/if}

            {$this->element('Admin.page/language')}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="data-extend-form" action="{ADMIN_PATH}/extend-data/{$code}/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet position-relative">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                {if !empty($form_config.rows)}
                    {foreach from = $form_config.rows item = row}
                        {if !empty($row.columns)}
                        <div class="row">
                            {foreach from = $row.columns item = column}
                                
                                    {assign var = column_value value = "{if !empty($column.column_value)}{$column.column_value}{else}12{/if}"}
                                    
                                    {if !empty($column.field)}
                                        <div class="col-12 col-md-{$column_value}">
                                            {foreach from = $column.field item = field}
                                                <div class="form-group">
                                                    <label>
                                                        {if !empty($field.name)}
                                                            {$field.name}
                                                        {/if}
                                                        {if !empty($field.required)}
                                                            <span class="kt-font-danger">*</span>
                                                        {/if}
                                                    </label>
                                                    
                                                    {$this->ExtendCollectionAdmin->generateInput($field, $lang)}
                                                </div>
                                            {/foreach}
                                        </div>
                                    {/if}                                    
                                
                            {/foreach}
                            </div>
                        {/if}
                    {/foreach}
                {/if}
            </div>
        </div>
        
    </form>
</div>