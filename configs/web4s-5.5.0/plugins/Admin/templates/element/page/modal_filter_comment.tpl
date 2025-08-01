<div id="modal-filter-comment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'tim_kiem')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close"></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'tu_khoa')}
                    </label>
                    <div class="kt-input-icon kt-input-icon--left">
                        <input id="nh-keyword" name="keyword" type="text" value="{if !empty($keyword)}{$keyword}{/if}" class="form-control form-control-sm" placeholder="{__d('admin', 'ho_ten')}, {__d('admin', 'so_dien_thoai')}, {__d('admin', 'email')} ..." autocomplete="off">
                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                            <span><i class="la la-search"></i></span>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    {$this->Form->select('status', $this->CommentAdmin->listStatus(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                </div>

                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                    <input type="checkbox" name="images" value="1"> {__d('admin', 'co_hinh_anh')}
                    <span></span>
                </label>

                <div class="form-group filter-rating d-none">
                    <label>
                        {__d('admin', 'danh_gia_sao')}
                    </label>
                    <select name="rating" class="form-control form-control-sm kt-selectpicker">
                        <option value="">{__d('admin', 'tat_ca')}</option>
                        <option value="5">5 {__d('admin', 'sao')}</option>
                        <option value="4">4 {__d('admin', 'sao')}</option>
                        <option value="3">3 {__d('admin', 'sao')}</option>
                        <option value="2">2 {__d('admin', 'sao')}</option>
                        <option value="1">1 {__d('admin', 'sao')}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'ngay_tao')}
                    </label>
                    <div class="input-group">
                        <input id="create_from" type="text" class="form-control form-control-sm kt_datepicker" name="create_from" autocomplete="off" placeholder="{__d('admin', 'tu')}" value="{if !empty($create_from)}{$create_from}{/if}">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                        </div>
                        <input id="create_to" type="text" class="form-control form-control-sm kt_datepicker" name="create_to" autocomplete="off" placeholder="{__d('admin', 'den')}" value="{if !empty($create_to)}{$create_to}{/if}">
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                <button class="btn btn-sm btn-primary" btn-search>
                    {__d('admin', 'tim_kiem')}
                </button>
            </div>
        </div>
    </div>
</div>