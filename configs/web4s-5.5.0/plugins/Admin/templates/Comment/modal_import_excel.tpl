<div id="import-excel-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header p-0 border-0"></div>

            <div class="modal-body">
                <h4 class="modal-title text-center">
                    {__d('admin', 'mau_file')}
                </h4>

                <a class="form-control-static text-center kt-shape-font-color-4" href="{ADMIN_PATH}/assets/document/file_import_customer.xls" download="">
                    {__d('admin', 'tai_mau_file_excel_va_nhap_thong_tin_binh_luan')}
                    <i class="fa fa-file-csv d-block pointer-event mt-15" style="font-size: 3rem;"></i>
                </a>

                <div class="kt-widget4 mt-30 mb-15">
                    <div class="kt-widget4__item">
                        <span class="kt-widget4__icon">
                            <i class="fa fa-check-circle text-success fs-14"></i>
                        </span>

                        <span class="kt-widget4__title kt-widget4__title--light">
                            {__d('admin', 'binh_luan_da_chuyen_doi')}
                        </span>

                        <span id="current-item-excel" class="kt-widget4__number kt-font-success">
                            0
                        </span>
                    </div>
                </div>

                <div class="progress mb-15 d-none">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 2%">2%</div>
                </div>

                <div class="alert alert-success d-none" role="alert">
                    {__d('admin', 'nhap_du_lieu_excel_thanh_cong')}
                </div>

                <div class="alert alert-danger d-none" role="alert"></div>

                <form id="import-excel-comment" action="{ADMIN_PATH}/comment/import-excel" method="POST" autocomplete="off">
                    <div class="form-group mb-30">
                        <label>
                            {__d('admin', 'file_import')}
                        </label>
                        <div class="custom-file">
                            <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.xlsm" class="custom-file-input">
                            <label class="custom-file-label selected" for="customFile"></label>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                            {__d('admin', 'dong')}
                        </button>
                        
                        <button id="btn-import-excel" type="button" class="btn btn-sm btn-primary" >
                            <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                            {__d('admin', 'cap_nhat')}
                        </button>
                    </div>  
                </form>
            </div>
        </div>
    </div>
</div>