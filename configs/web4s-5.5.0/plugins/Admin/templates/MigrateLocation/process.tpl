<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}
                    {$title_for_layout}
                {/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">          
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">    

    <div class="kt-portlet nh-portlet position-relative">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Chuyển đổi dữ liệu
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-10">
                            <span class="kt-font-bolder">
                                Chọn bảng migrate
                            </span>
                        </div>
                    </div>

                    <table nh-table="tables" class="table table-hover table-compare">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-70">
                                    Dữ liệu chuẩn
                                </th>
                                <th class="w-30"></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-10">
                            <span class="kt-font-bolder">
                                Fields
                            </span>
                        </div>
                    </div>

                    <table nh-table="fields" from-table="" class="table table-hover table-compare">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-80">Hệ thống</th>
                                <th class="w-20 text-right">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>

                <div nh-wrap="process-form" class="col-md-4">
                    
                </div>
            </div>
            
        </div>
    </div>
</div>