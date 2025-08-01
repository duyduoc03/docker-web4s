{assign var = list_status value = $this->ListConstantAdmin->listStatusOrder()}

{if !empty($logs)}
    {foreach from = $logs item = log}
        {assign var = icon value = 'fa fa-file-medical kt-font-dark'}
        {assign var = class_status value = 'kt-badge--dark'}

        {if !empty($log.status) && $log.status == {NEW_ORDER}}
            {$icon = 'fa fa-file-alt kt-font-danger'}
            {$class_status = 'kt-badge--danger'}
        {elseif !empty($log.status) && $log.status == {CONFIRM}}
            {$icon = 'fa fa-check-square kt-font-warning'}
            {$class_status = 'kt-badge--warning'}
        {elseif !empty($log.status) && $log.status == {PACKAGE}}
            {$icon = 'fa fa-box kt-font-brand'}
            {$class_status = 'kt-badge--brand'}
        {elseif !empty($log.status) && $log.status == {EXPORT}}
            {$icon = 'fa fa-truck-loading kt-font-brand'}
            {$class_status = 'kt-badge--brand'}
        {elseif !empty($log.status) && $log.status == {DONE}}
            {$icon = 'fa fa-check-circle kt-font-success'}
            {$class_status = 'kt-badge--success'}
        {/if}

        <div class="kt-notes__item pb-20">
            <div class="kt-notes__media">
                <span class="kt-notes__icon">
                    <i class="{if !empty($icon)}{$icon}{/if}"></i>
                </span>
            </div>

            <div class="kt-notes__content">
                <div class="kt-notes__section">
                    <div class="kt-notes__info">
                        <span class="kt-notes__desc">
                            {if !empty($log.created)}
                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($log.created)}
                            {/if}
                        </span>
                        <span class="kt-badge {if !empty($class_status)}{$class_status}{/if} kt-badge--inline">
                            {if !empty($list_status) && !empty($log.status) && !empty($list_status[$log.status])}
                                {$list_status[$log.status]}
                            {/if}
                        </span>
                    </div>
                </div>
                <span class="kt-notes__body">
                    {if !empty($log.description_admin)}
                        {$log.description_admin}
                    {/if}
                </span>
            </div>
        </div>
    {/foreach}
{/if}