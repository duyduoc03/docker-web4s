{if !empty($cities)}
    {foreach from = $cities key = k item = city}
        <tr>
            <td>
                {if !empty($city.id)}
                    {$city.id}
                {/if}
            </th>
            <td>
                <div nh-text="origin">
                    {if !empty($city.name)}
                        {if !empty($city.extend)}
                            {$city.extend}
                        {/if}
                        {$city.name}
                    {/if}
                </div>
            </td>
            <td>
                <div nh-text="migrate">
                    {if !empty($city.migrate_name)}
                        {if !empty($city.migrate_extend)}
                            {$city.migrate_extend}
                        {/if}
                        {$city.migrate_name}
                    {/if}
                </div>
            </td>
            <td>
                {if !empty($city.changed) && $city.changed == 'info'}
                    <span class="kt-badge kt-badge--brand kt-badge--inline">
                        {$city.changed}
                    </span>
                {/if}

                {if !empty($city.changed) && $city.changed == 'merge'}
                    <span class="kt-badge kt-badge--danger kt-badge--inline">
                        {$city.changed}
                    </span>
                {/if}
            </td>
            <td class="text-right">
                <span nh-btn="show-migrate-modal" data-object="city" data-id="{if !empty($city.id)}{$city.id}{/if}" migrate-id="{if !empty($city.migrate_id)}{$city.migrate_id}{/if}" class="btn btn-primary btn-sm btn-icon h-20 w-20px" title="Đồng bộ tỉnh thành">
                    <i class="fa fa-edit fs-10"></i>
                </span>

                <span nh-btn="load-list-districts" city-id="{if !empty($city.id)}{$city.id}{/if}" migrate-city-id="{if !empty($city.migrate_id)}{$city.migrate_id}{/if}" class="btn btn-secondary btn-sm btn-icon h-20 w-20px" title="Xem quận huyện">
                    <i class="fa fa-angle-double-right fs-10"></i>
                </span>
            </td>
        </tr>
    {/foreach}
{/if}