{if !empty($payment.logs)}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    {__d('admin', 'trang_thai')}
                </th>
                <th class="text-right">
                    {__d('admin', 'so_tien')}
                </th>
                <th>
                    {__d('admin', 'ma_tham_chieu')}
                </th>
                <th>
                    {__d('admin', 'ghi_chu')}
                </th>
                <th>
                    {__d('admin', 'nguoi_cap_nhat')}
                </th>
                <th>
                    {__d('admin', 'ngay_cap_nhat')}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$payment.logs item=log key=key}
                <tr>
                    <td>
                        {if isset($log.status) && $log.status == 0}
                            <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill">
                                {__d('admin', 'da_huy')}
                            </span>
                        {/if}
                        {if isset($log.status) && $log.status == 1}
                            <span class="kt-badge kt-badge--success kt-font-bold kt-badge--inline kt-badge--pill">
                                {__d('admin', 'thanh_cong')}
                            </span>
                        {/if}
                        {if isset($log.status) && $log.status == 2}
                            <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill">
                                {__d('admin', 'cho_duyet')}
                            </span>
                        {/if}
                    </td>
                    <td class="text-right">
                        {if !empty($log.amount)}
                            {$log.amount|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($log.reference)}
                            {$log.reference}
                        {/if}
                    </td>
                    <td>
                        {if !empty($log.note)}
                            {$log.note}
                        {/if}
                    </td>
                    <td>
                        {if !empty($log.user_full_name)}
                            {$log.user_full_name}
                        {/if}
                    </td>
                    <td>
                        {if !empty($log.created)}
                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($log.created)}
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}