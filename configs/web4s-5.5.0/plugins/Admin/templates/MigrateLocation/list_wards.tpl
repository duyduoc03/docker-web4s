{if !empty($wards)}
    {foreach from = $wards key = k item = ward}
        <tr>
            <td>
                {if !empty($ward.id)}
                    {$ward.id}
                {/if}
            </th>
            <td>
                <div nh-text="origin">
                    {if !empty($ward.name)}
                        {if !empty($ward.extend)}
                            {$ward.extend}
                        {/if}
                        {$ward.name}
                    {/if}
                </div>
            </td>
            <td>
                <div nh-text="migrate">
                    {if !empty($ward.migrate_name)}
                        {if !empty($ward.migrate_extend)}
                            {$ward.migrate_extend}
                        {/if}
                        {$ward.migrate_name}
                    {/if}
                </div>
            </td>
            <td>
                {if !empty($ward.changed) && $ward.changed == 'info'}
                    <span class="kt-badge kt-badge--brand kt-badge--inline">
                        {$ward.changed}
                    </span>
                {/if}

                {if !empty($ward.changed) && $ward.changed == 'merge'}
                    <span class="kt-badge kt-badge--danger kt-badge--inline">
                        {$ward.changed}
                    </span>
                {/if}
            </td>
            <td class="text-right">
                <span nh-btn="show-migrate-modal" data-object="ward" data-id="{if !empty($ward.id)}{$ward.id}{/if}" migrate-id="{if !empty($ward.migrate_id)}{$ward.migrate_id}{/if}" class="btn btn-primary btn-sm btn-icon h-20 w-20px" title="Đồng bộ phường xã">
                    <i class="fa fa-edit fs-10"></i>
                </span>
            </td>
        </tr>
    {/foreach}
{/if}