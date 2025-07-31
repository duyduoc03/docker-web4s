<div class="kt-widget2">
    {if !empty($logs)}
        {foreach from = $logs item = log}
            {assign var = recordId value = "{if !empty($log.record_id)}{$log.record_id}{/if}"}
            {assign var = subType value = "{if !empty($log.sub_type)}{$log.sub_type}{/if}"}
            {assign var = version value = "{if !empty($log.version)}{$log.version}{/if}"}

            {assign var = action value = "{if !empty($log.action)}{$log.action}{/if}"}
            {assign var = action_class value = 'kt-widget2__item--primary'}
            {assign var = description value = {__d('admin', 'cap_nhat_ban_ghi')}}
            {if $action == 'add'}
                {$action_class = 'kt-widget2__item--success'}
                {assign var = description value = {__d('admin', 'tao_moi_ban_ghi')}}
            {/if}

            {if $action == 'delete'}
                {$action_class = 'kt-widget2__item--danger'}
                {assign var = description value = {__d('admin', 'xoa_ban_ghi')}}
            {/if}

            <div class="kt-widget2__item {$action_class} pl-30">
                <div class="kt-widget2__info">
                    <span class="kt-widget2__title">
                        {if !empty($log.user.full_name)}
                            <span class="text-primary">
                                {$log.user.full_name}
                            </span>
                        {/if}

                        {$description}
                    </span>
                    <span class="kt-widget2__username">
                        {if !empty($log.created_label)}
                            {$log.created_label}
                        {/if}
                    </span>
                </div>

                {if !empty($version)}
                    <div class="kt-widget2__actions">
                        <i nh-btn="show-data-version" version="{$version}" class="fa fa-eye cursor-p fs-14 text-primary"></i>
                    </div>
                {/if}
            </div>
        {/foreach}

        {if !empty($pagination_info.pages) && $pagination_info.pages > 1}
            <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-20 mb-10"></div>            
            {$this->element('layout/pagination', ['pagination' => $pagination_info])}
        {/if}
    {else}
        {__d('admin', 'khong_co_thong_tin_nao_cap_nhat')} 
    {/if}
</div>