{if !empty($fields)}
    {foreach from = $fields key = $field item = exist}
        <tr>
            <td>
                {if !empty($field)}
                    {$field}
                {/if}
            </td>
            <td class="text-right">
                {if !empty($exist)}
                    <i class="fa fa-check-circle text-success"></i>
                {else}
                    <i class="fa fa-window-close text-danger"></i>
                {/if}
            </td>
        </tr>
    {/foreach}
{/if}