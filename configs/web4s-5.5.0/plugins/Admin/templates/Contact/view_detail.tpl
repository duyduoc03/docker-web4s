{if !empty($contact.value)}
    <div class="contact-detail">
        <table class="table">
            {foreach from = $contact.value item = value key = code}       
                <tr>
                    <td class="{if $value@first}border-top-0{/if} w-30">
                        {if !empty($fields[$code])}
                            {$fields[$code]}
                        {else}
                            {$code}
                        {/if}    
                    </td>
                    <td class="{if $value@first}border-top-0{/if}">
                        <span class="kt-font-bolder">
                            {if is_array($value)}
                                {foreach from = $value item = val}    
                                    {$val} <br>
                                {/foreach}
                            {else}
                                {$value}
                            {/if}
                        </span>
                    </td>
                </tr>
            {/foreach}
            <tr>
                <td>{__d('admin', 'ngay_nhan')}</td>
                <td>
                    <span class="kt-font-bolder">
                        {if !empty($contact.created)}
                            <i>{$contact.created}</i>
                        {/if}
                    </span>
                </td>
            </tr>
        </table>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}</span>
{/if}

