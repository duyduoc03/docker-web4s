<div class="table-responsive table-responsive-locales">
    <table class="table table-striped table-bordered table-hover table-checkable mb-0">
        <thead>
            <tr>
                {if !empty($languages)}
                    {foreach from = $languages key = lang item = language}
                        {if !empty($language)}
                            <th class="w-33">
                                <div class="item-key">
                                    <div class="kt-portlet__head-label">
                                        <span class="icon">
                                            <img src="{ADMIN_PATH}/assets/media/flags/{$lang}.svg" alt="{$language}" class="img-fluid"></img>
                                        </span>
                                        <div class="kt-portlet__head-title" style="white-space: nowrap;">
                                            {$language}
                                        </div>
                                    </div>
                                    {if !empty($lang) && $lang != $lang_default}
                                        <span class="input-check-all btn btn-sm btn-label-brand btn-bold cursor ml-4" nh-btn="data-extend-translate-column" nh-language-column="{$lang}" nh-language-default="{$lang_default}">
                                            {__d('admin', 'dich')} {$language}
                                        </span>
                                    {/if}
                                </div>
                            </th>
                        {/if}
                    {/foreach}
                {/if}
            </tr>
        </thead>

        <tbody>
            {if !empty($data)}
                {foreach from = $data key = key item = item}
                    <tr>
                        {if !empty($languages)}
                            {foreach from = $languages key = lang item = language}
                                {assign var = type value = $type}
                                {if !empty($item[$lang]['type'])}
                                    {assign var = type value = $item[$lang]['type']}
                                {/if}
                                {assign var = id value = ''}
                                {if !empty($item[$lang]['id'])}
                                    {assign var = id value = $item[$lang]['id']}
                                {/if}

                                {assign var = label value = ''}
                                {if !empty($item[$lang]['label'])}
                                    {assign var = label value = $item[$lang]['label']}
                                {/if}
                                {assign var = code value = ''}
                                {if !empty($item[$lang]['code'])}
                                    {assign var = code value = $item[$lang]['code']}
                                {/if}
                                <td  class="w-33" style="vertical-align: bottom;"> 
                                    <div class="form-group">
                                        {if $type == 'locales_po' || $type == 'locales_js' || $type == 'block'}
                                            <label class="d-flex">Key: <span class="label-key pl-1" width-label="">{if !empty($id)}{$id}{else}{$key}{/if}</span></label>
                                        {/if}
                                        <div class="input-group">
                                            <input nh-input-change-data nh-data-type="{$type}" nh-data-lang="{$lang}" nh-data-id="{if !empty($id)}{$id}{else}{$key}{/if}" nh-data-code="{$code}" class="form-control {if empty($label)}border-danger-5{/if}" pattern="[^<>]*" value='{$label}'/>
                                            <div class="input-group-append">
                                                {if !empty($item[$lang]['edit_url'])}
                                                    <a style="width: 40px;" target="_blank" class="input-group-text text-center pr-10" href="{$item[$lang]['edit_url']}" title="{__d('admin', 'sua')}">
                                                        <i class="fs-12 fa fa-external-link-alt"></i>
                                                    </a>
                                                {/if}
                                                {if !empty($lang_default) && $lang != $lang_default}
                                                    <span nh-btn="data-extend-translate" nh-language-default="{if !empty($lang_default)}{$lang_default}{/if}" class="input-group-text cursor-p" title="{__d('admin', 'dich')}">
                                                        <i class="fa fa-language kt-font-brand"></i>
                                                    </span>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            {/foreach}
                        {/if}
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="100%" class="text-center">
                        {__d('admin', 'khong_co_du_lieu')}
                    </td>
                </tr>
            {/if}
            
        </tbody>
    </table>
</div>

<div class="kt-todo__foot">
    <div class="kt-todo__toolbar">
        <div class="kt-todo__controls">
            {$this->element('Admin.page/pagination')}
        </div>
    </div>
</div>