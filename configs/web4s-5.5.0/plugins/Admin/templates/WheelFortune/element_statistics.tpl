<div class="kt-portlet__body">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover table-checkable bg-light text-center mb-20">
            <thead class="">
                <tr>
                    <td class="text-left">{__d('admin', 'giai_thuong')}</td>
                    {foreach from = $prize_winning item = item}
                        <td>
                            {$item.prize_name}
                        </td>
                    {/foreach}
                    <td class="text-left">{__d('admin', 'tong')}</td>
                </tr>
            </thead>
            <tbody>
                <tr class="table-success">
                    <td class="text-left">{__d('admin', 'gioi_han')}</td>
                    {foreach from = $prize_winning item = item}
                        <td>
                            {$item.limit_prize}
                        </td>
                    {/foreach}
                    <td>
                        {$total_prize}
                    </td>
                </tr>
                <tr class="table-warning">
                    <td class="text-left">{__d('admin', 'quay_thuong')}</td>
                    {foreach from = $prize_winning item = item}
                        <td>
                            {$item.number_winning}
                        </td>
                    {/foreach}
                    <td>
                        {$number_winning}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mb-5">
        {__d('admin', 'lien_he_nhan_giai')}: <b class="text-primary">{$number_log}</b>
    </div>

    <div class="mb-5">
        {__d('admin', 'ti_le_chuyen_doi')}: <b class="text-primary">{$rate_winning}%</b>
    </div>
</div>