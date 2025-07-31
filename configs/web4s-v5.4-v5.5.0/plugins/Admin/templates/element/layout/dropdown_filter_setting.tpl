{assign var = filters value = $this->UserAdmin->getSettingForListView($path_menu, 'filter')}
{assign var = locales value = $this->UserAdmin->getLocaleSettingForUser()}
{assign var = user_filters value = $this->UserAdmin->getSettingForUser($path_menu, 'filter')}

{if !empty($filters)}   
    <div class="dropdown">
        <span data-toggle="dropdown" class="cursor-p">
            <i class="fa fa-plus-circle fs-10"></i> {__d('admin', 'them_bo_loc')}
        </span>
        
        <div nh-wrap="dropdown-filter" class="dropdown-menu px-3 w-25" >
            <form action="{ADMIN_PATH}/user/save-setting-view" method="POST" autocomplete="off" >
                <input name="path_menu" value="{$path_menu}" type="hidden" />
                <input name="type" value="filter" type="hidden" />
                <div class="row">
                    {foreach from = $filters key = code item = filter}
                        {assign var = show value = 0}
                        {if !empty($filter.show)}
                            {$show = 1}
                        {/if}
                        
                        {if isset($user_filters[{$code}].show)}
                            {$show = $user_filters[{$code}].show}
                        {/if} 
                        
                        <div class="col-6 mb-2">
                            <label class="kt-checkbox kt-checkbox--brand" >
                                <input name="filter[{$code}]" {if !empty($show)} checked{/if} value="1" class="check-single" type="checkbox" />
                                  {__d('admin', "{$locales[{$code}]}")}
                                <span></span>
                            </label>
                        </div>
                     {/foreach}
                </div>

                <span nh-btn="save-filter-config" class="btn btn-sm btn-brand " >
                    <i class="fa fa-check"></i>
                    {__d('admin', 'xac_nhan')}
                </span> 
            </form>
        </div>
    </div>
{/if}