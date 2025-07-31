{if !empty($districts)}
    {foreach from = $districts key = k item = district}
        <tr>
            <td>
                {if !empty($district.id)}
                    {$district.id}
                {/if}
            </th>
            <td>
                <div nh-text="origin">
                    {if !empty($district.name)}
                        {if !empty($district.extend)}
                            {$district.extend}
                        {/if}
                        {$district.name}
                    {/if}
                </div>
            </td>
            <td>
                <div nh-text="migrate">
                    {if !empty($district.migrate_name)}
                        {if !empty($district.migrate_extend)}
                            {$district.migrate_extend}
                        {/if}
                        {$district.migrate_name}
                    {/if}
                </div>
            </td>
            <td>
                {if !empty($district.changed) && $district.changed == 'info'}
                    <span class="kt-badge kt-badge--brand kt-badge--inline">
                        {$district.changed}
                    </span>
                {/if}

                {if !empty($district.changed) && $district.changed == 'merge'}
                    <span class="kt-badge kt-badge--danger kt-badge--inline">
                        {$district.changed}
                    </span>
                {/if}

                {if !empty($district.changed) && $district.changed == 'create'}
                    <span class="kt-badge kt-badge--success kt-badge--inline">
                        {$district.changed}
                    </span>
                {/if}
            </td>
            <td class="text-right">
                <span nh-btn="show-migrate-modal" data-object="district" data-id="{if !empty($district.id)}{$district.id}{/if}" migrate-id="{if !empty($district.migrate_id)}{$district.migrate_id}{/if}" class="btn btn-primary btn-sm btn-icon h-20 w-20px" title="Đồng bộ quận huyện">
                    <i class="fa fa-edit fs-10"></i>
                </span>

                <span nh-btn="load-list-wards" district-id="{if !empty($district.id)}{$district.id}{/if}" migrate-district-id="{if !empty($district.migrate_id)}{$district.migrate_id}{/if}" class="btn btn-primary btn-sm btn-icon h-20 w-20px" title="Xem quận huyện">
                    <i class="fa fa-angle-double-right fs-10"></i>
                </span>
            </td>
        </tr>
    {/foreach}
{/if}