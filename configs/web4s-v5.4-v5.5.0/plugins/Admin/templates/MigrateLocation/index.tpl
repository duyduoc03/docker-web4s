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
                    Đồng bộ dữ liệu
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-10">
                            <span class="kt-font-bolder">
                                Tỉnh thành
                            </span>
                        </div>
                    </div>

                    <table nh-table="cities" class="table table-hover table-compare">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-5">ID</th>
                                <th class="w-30">Hệ thống</th>
                                <th class="w-30">Dữ liệu chuẩn</th>
                                <th class="w-10">TT</th>
                                <th class="w-20 text-right">
                                    <span nh-btn="reload-list" data-type="city" class="btn btn-success btn-sm btn-icon h-20 w-20px" title="Làm mới">
                                        <i class="fa fa-sync-alt fs-10"></i>
                                    </span>
                                </th>
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
                                Quận huyện
                            </span>
                        </div>
                    </div>

                    <table nh-table="districts" from-city="" from-city-migrate="" class="table table-hover table-compare">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-5">ID</th>
                                <th class="w-30">Hệ thống</th>
                                <th class="w-30">Dữ liệu chuẩn</th>
                                <th class="w-10">TT</th>
                                <th class="w-20 text-right">
                                    <span nh-btn="reload-list" data-type="district" class="btn btn-success btn-sm btn-icon h-20 w-20px" title="Làm mới">
                                        <i class="fa fa-sync-alt fs-10"></i>
                                    </span>
                                </th>
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
                                Phường xã
                            </span>
                        </div>
                    </div>

                    <table nh-table="wards" from-district="" from-district-migrate="" class="table table-hover table-compare">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-5">ID</th>
                                <th class="w-30">Hệ thống</th>
                                <th class="w-30">Dữ liệu chuẩn</th>
                                <th class="w-10">TT</th>
                                <th class="w-20 text-right">
                                    <span nh-btn="reload-list" data-type="ward" class="btn btn-success btn-sm btn-icon h-20 w-20px" title="Làm mới">
                                        <i class="fa fa-sync-alt fs-10"></i>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <div class="kt-portlet nh-portlet position-relative">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Import dữ liệu
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p class="kt-font-bolder mb-5">
                            Tải về tại: https://danhmuchanhchinh.gso.gov.vn/Default.aspx
                        </p>

                        <i class="text-danger">
                            Lưu ý: Chia đôi file thành 2 để import, tránh bị lỗi tràn bộ nhớ
                        </i>
                    </div>
            
                    <div class="form-group">
                        <label>
                            {__d('admin', 'file_import')}
                        </label>
                        <div class="custom-file">
                            <input name="excel_file" type="file" accept=".xlsx,.xls,.xlsm" class="custom-file-input">
                            <label class="custom-file-label selected" for="excel_file"></label>
                        </div>
                    </div>

                    <div class="form-group">                    
                        <span nh-btn="import-data" class="btn btn-sm btn-primary">
                            <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                            Cập nhật
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="kt-font-bolder mb-5">
                        Nếu đã đồng bộ dữ liệu xong, thực hiện chuyển đổi dữ liệu cũ của các bảng <a href="{ADMIN_PATH}/migrate-location/process">tại đây</a> 
                    </p>
                </div>
            </div>            
        </div>
    </div>
</div>

<div id="merge-city-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Cập nhật dữ liệu
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer p-10">
               <span nh-btn="migrate-location" class="btn btn-sm btn-primary">
                    Cập nhật
                </span>
            </div>
        </div>
    </div>
</div>