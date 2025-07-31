<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-xl-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}" autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status', $this->ListConstantAdmin->listStatus(), ['id'=>'nh_status', 'empty' => {__d('admin', 'trang_thai')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                <select id="pay_status" name="pay_status" class="form-control form-control-sm kt-selectpicker" >
                                    <option value>-- {__d('admin', 'trang_thai_thanh_toan')} --</option>
                                    <option value="debt">{__d('admin', 'con_no')}</option>
                                    <option value="completed">{__d('admin', 'thanh_toan_hoan_tat')}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-icon collapse-search-advanced" data-toggle="collapse" data-target="#collapse-search-advanced">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="collapse-search-advanced" class="collapse collapse-search-advanced-content">
        <div class="kt-margin-t-20">
            <div class="form-group row">
                <div nh-filter-item="city_id" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tinh_thanh')}
                        </label>
                        {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'autocomplete' => 'off', 'data-live-search' => true])}
                    </div>
                </div>

                <div nh-filter-item="district_id" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'quan_huyen')}
                        </label>
                        {$this->Form->select('district_id', [], ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'autocomplete' => 'off', 'data-live-search' => true])}
                    </div>
                </div>

                <div nh-filter-item="ward_id" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'phuong_xa')}
                        </label>
                        {$this->Form->select('ward_id', [], ['id' => 'ward_id', 'empty' => "-- {__d('admin', 'phuong_xa')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'autocomplete' => 'off', 'data-live-search' => true])}
                    </div>
                </div>

                <div nh-filter-item="partner_affiliate" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'doi_tac')}
                        </label>
                        
                        <div class="kt-form__group">
                            {$this->Form->select('is_partner_affiliate', $this->ListConstantAdmin->listStatusAffiliate(), ['id'=>'is_partner_affiliate', 'empty' => {__d('admin', 'trang_thai')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>    
                    </div>
                </div>

                <div nh-filter-item="tracking_source" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'nguon')}
                        </label>
                        
                        <div class="kt-form__group">
                            <input id="tracking_source" name="tracking_source" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nguon')}" autocomplete="off">
                        </div>    
                    </div>
                </div>

                <div nh-filter-item="source" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'nguon_don_hang')}
                        </label>
                        <div class="input-group">
                            <input id="source" name="source" type="text" class="form-control form-control-sm tagify-input" placeholder="{__d('admin', 'nguon_don_hang')}" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div nh-filter-item="order" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'trang_thai_don_hang')}
                        </label>
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status_order', $this->ListConstantAdmin->listStatusOrder(), ['id'=>'status_order', 'empty' => "-- {__d('admin', 'trang_thai_don_hang')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                            </div>
                        </div>
                    </div>
                </div>

                <div nh-filter-item="total_order" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tong_don_hang')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="total_order_from" type="text" class="form-control form-control-sm number-input" name="total_order_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-long-arrow-alt-right"></i>
                                </span>
                            </div>
                            <input id="total_order_to" type="text" class="form-control form-control-sm number-input" name="total_order_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div nh-filter-item="total_price" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tong_tien')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="total_from" type="text" class="form-control form-control-sm number-input" name="total_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-long-arrow-alt-right"></i>
                                </span>
                            </div>
                            <input id="total_to" type="text" class="form-control form-control-sm number-input" name="total_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div nh-filter-item="point" class="col-md-3">
                    <div class="form-group">
                        <label class="text-capitalize">
                            {__d('admin', 'diem')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="point_from" type="text" class="form-control form-control-sm number-input" name="point_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-long-arrow-alt-right"></i>
                                </span>
                            </div>
                            <input id="point_to" type="text" class="form-control form-control-sm number-input" name="point_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div nh-filter-item="point_promotion" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'diem_khuyen_mai')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="point_promotion_from" type="text" class="form-control form-control-sm number-input" name="point_promotion_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-long-arrow-alt-right"></i>
                                </span>
                            </div>
                            <input id="point_promotion_to" type="text" class="form-control form-control-sm number-input" name="point_promotion_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div nh-filter-item="created" class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ngay_tao')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="create_from" type="text" class="form-control form-control-sm kt_datepicker" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-ellipsis-h"></i>
                                </span>
                            </div>
                            <input id="create_to" type="text" class="form-control form-control-sm kt_datepicker" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>

            {$this->element('layout/dropdown_filter_setting')}
        </div>
    </div>
</div> 