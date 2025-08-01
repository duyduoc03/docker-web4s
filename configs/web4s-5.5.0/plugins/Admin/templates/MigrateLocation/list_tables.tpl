{if !empty($tables)}
    {foreach from = $tables key = k item = table}
        <tr>
            <td>
                {if !empty($table)}
                    {$table}
                {/if}
            </td>                      
            <td class="text-right">
                <span nh-btn="load-fields" nh-table="{if !empty($table)}{$table}{/if}" class="btn btn-secondary btn-sm btn-icon h-20 w-20px">
                    <i class="fa fa-angle-double-right fs-10"></i>
                </span>
            </td>
        </tr>
    {/foreach}
{/if}