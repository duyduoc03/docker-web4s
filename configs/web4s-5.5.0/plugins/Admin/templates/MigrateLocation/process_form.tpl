<div class="row">
    <div class="col-12 mb-10">
        <span class="kt-font-bolder">
            Kiểm tra trạng thái bảng
        </span>
    </div>
</div>

<table nh-table="processing" class="table table-hover">
    <thead class="thead-light">
        <tr>
            <th class="w-80">Thông tin</th>
            <th class="w-20 text-right">Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                Kiểm tra thông tin:
                {if !empty($table_name)}
                    <strong>
                        {$table_name}
                    </strong>
                {/if}                
            </td>
            <td class="text-right">
                <span nh-btn="check-status" data-table="{if !empty($table_name)}{$table_name}{/if}" class="btn btn-primary btn-sm">
                    Kiểm tra
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    Tạo field 
                    <strong>
                        "synced"
                    </strong>
                </p>
                <p class="mb-0">
                    <i>
                        Field "synced" được thêm dạng "int" và giá trị mặc định là 0.
                    </i>
                </p>
                <p class="mb-0">
                    <i>
                        Sau khi thêm field nhớ xóa cache để kiểm tra lại
                    </i>
                </p>
            </td>
            <td nh-label="field-synced" class="text-right">
                {if isset($field_synced)}
                    {if !empty($field_synced)}
                        <i class="fa fa-check-circle text-success"></i>
                    {else}
                        <i class="fa fa-window-close text-danger"></i>
                    {/if}
                {else}
                    <i class="text-muted">
                        Chưa kiểm tra
                    </i>
                {/if}
            </td>
        </tr>

        <tr>
            <td>
                Đã đồng bộ
            </td>
            <td class="text-right">
                <span nh-label="synced-record">
                    ...
                </span>/
                <span nh-label="total-record">
                    ...
                </span>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div nh-wrap="progress" class="progress progress-sm mt-15">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
                </div>

                <div class="row">
                    <div class="col-12 text-right mt-10">
                        <span nh-btn="processing" data-table="{if !empty($table_name)}{$table_name}{/if}" class="btn btn-primary btn-sm disabled">
                            Đồng bộ
                        </span>
                        <span nh-btn="pause" class="btn btn-warning btn-sm disabled">
                            Tạm dừng
                        </span>
                    </div>
                </div>                
            </td>
        </tr>
    </tbody>
</table>
