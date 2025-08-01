{assign var = fields value = $this->UserAdmin->getSettingForListView($path_menu, 'field')}
{assign var = locales value = $this->UserAdmin->getLocaleSettingForUser()}
{assign var = user_fields value = $this->UserAdmin->getSettingForUser($path_menu, 'field')}

{$fields = $this->UserAdmin->sortColumnForListView($fields, $user_fields)}

{if !empty($fields)}

    <div id="setting-field-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{ADMIN_PATH}/user/save-setting-view" method="POST" autocomplete="off" >
                        <input name="path_menu" value="{$path_menu}" type="hidden" />
                        <input name="type" value="field" type="hidden" />

                        <div class="row sortable-fields">
                            {foreach from = $fields key = code item = field} 
                                {assign var = show value = 0}
                                {assign var = not_change value = $field.not_change}
                                {if !empty($field.show)}
                                    {$show = 1}
                                {/if}
                                {if isset($user_fields[{$code}].show)}
                                    {$show = $user_fields[{$code}].show}
                                {/if} 

                                <div class="col-6 mb-10 setting-field {if $code == 'id' || $code == 'status'}disable-sort{/if} ">
                                    <span class="icon-sort-field " title="{__d('admin', 'sap_xep_hien_thi')}">
                                        <i class="fa fa-sort fs-11 mr-1"></i>
                                    </span>
                                    <input name="sorts[{$code}]" class="sort-fields" value="1" type="hidden" />    
                                    <label class="kt-checkbox {if !empty($not_change)}kt-checkbox--disabled{else}kt-checkbox--brand{/if}">
                                        <input name="fields[{$code}]" {if !empty($show) }checked{/if} {if !empty($not_change)}disabled checked {/if}  value="1" class="check-single item-checkbox" type="checkbox" />
                                        {__d('admin', "{$locales[{$code}]}")}
                                        <span></span>
                                    </label>
                                </div> 
                            {/foreach}
                        </div>
                        
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <div class="main-settings-select-links">
                        <span nh-btn="check-all-field" class="checkbox-select-all ">
                            {__d('admin', 'chon_tat_ca')}
                        </span>
                        <span nh-btn="uncheck-all-field" class="checkbox-unselect-all">
                            {__d('admin', 'bo_chon_tat_ca')}
                        </span>
                    </div>

                    <div>
                        <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                            {__d('admin', 'dong')}
                        </span>
                        
                        <span nh-btn="save-list-field-config" class="btn btn-sm btn-brand " >
                            <i class="fa fa-check"></i>
                            {__d('admin', 'xac_nhan')}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
