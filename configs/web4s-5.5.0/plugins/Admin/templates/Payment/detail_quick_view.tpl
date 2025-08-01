{if !empty($payment)}
    <div wrap-info>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'ma_giao_dich')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.code)}
                                {$payment.code}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'trang_thai')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        {if isset($payment.status) && $payment.status == 0}
                            <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                {__d('admin', 'da_huy')}
                            </span>
                        {/if}
                        {if isset($payment.status) && $payment.status == 1}
                            <span class="kt-badge kt-badge--success kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                {__d('admin', 'thanh_cong')}
                            </span>
                        {/if}
                        {if isset($payment.status) && $payment.status == 2}
                            <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                {__d('admin', 'cho_duyet')}
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'loai_phieu')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if isset($payment.type) && $payment.type == 0}
                                {__d('admin', 'phieu_chi')}
                            {elseif isset($payment.type) && $payment.type == 1}
                                {__d('admin', 'phieu_thu')}
                            {else}
                            ...
                            {/if}
                        </span>
                    </div>
                </div>                           

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {if isset($payment.type) && $payment.type == 0}
                            {__d('admin', 'ten_nguoi_nhan')}
                        {/if}
                        {if isset($payment.type) && $payment.type == 1}
                            {__d('admin', 'ten_nguoi_nop')}
                        {/if}
                    </label>

                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">                                       
                            {if !empty($payment.full_name)}
                                {if isset($payment.object_type) && $payment.object_type == CUSTOMER}
                                    <a href="{ADMIN_PATH}/customer/detail{if !empty($payment.object_id)}/{$payment.object_id}{/if}">{$payment.full_name}</a>
                                {else}
                                    {$payment.full_name}
                                {/if}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                {if !empty($payment.order)}
                    <div class="form-group form-group-xs row">
                        <label class="col-xl-4 col-lg-4 col-form-label">
                            {__d('admin', 'don_hang')}
                        </label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($payment.order.id)}
                                    <a href="{ADMIN_PATH}/order/detail/{$payment.order.id}" target="_blank" >
                                        {if !empty($payment.order.code)}
                                            {$payment.order.code}
                                        {/if}
                                    </a>
                                {/if}
                            </span>
                        </div>
                    </div>
                {/if}
            </div>

            <div class="col-lg-6">
                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'tong_tien_don_hang')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.order.total)}
                                {if isset($payment.type) && $payment.type == 0}
                                    <div class="text-danger">
                                        {$payment.order.total|number_format:0:".":","}
                                    </div>
                                {/if}
                                {if isset($payment.type) && $payment.type == 1}
                                    <div class="text-success">
                                        {$payment.order.total|number_format:0:".":","}
                                    </div>
                                {/if}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'so_tien')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.amount)}
                                {if isset($payment.type) && $payment.type == 0}
                                    <div class="text-danger">
                                        {$payment.amount|number_format:0:".":","}
                                    </div>
                                {/if}
                                {if isset($payment.type) && $payment.type == 1}
                                    <div class="text-success">
                                        {$payment.amount|number_format:0:".":","}
                                    </div>
                                {/if}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'phuong_thuc_thanh_toan')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {assign var = payment_method value = $this->PaymentAdmin->getListPaymentsForDropdown()}
                            {if !empty($payment.payment_method)}
                                {$payment_method[$payment.payment_method]}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                {if !empty($payment.payment_gateway_code)}                                
                    <div class="form-group form-group-xs row">
                        <label class="col-xl-4 col-lg-4 col-form-label">
                            {__d('admin', 'cong_thanh_toan')}
                        </label>

                        <div class="col-xl-8 col-lg-8">
                            <span class="form-control-plaintext kt-font-bolder">
                                {assign var = list_gateway value = $this->PaymentAdmin->getListGateWay($lang)}
                                {if !empty($list_gateway[$payment.payment_gateway_code])}
                                    {$list_gateway[$payment.payment_gateway_code]}
                                {else}
                                    ...
                                {/if}
                            </span>
                        </div>
                    </div>
                {/if}

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'ma_tham_chieu')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.reference)}
                                {$payment.reference}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row d-none">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'hach_toan_ket_qua_kinh_doanh')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if isset($payment.counted) && $payment.counted == 0}
                                {__d('admin', 'khong')}
                            {/if}
                            {if isset($payment.counted) && $payment.counted == 1}
                                {__d('admin', 'co')}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-4 col-lg-4 col-form-label">
                        {__d('admin', 'ghi_chu')}
                    </label>
                    <div class="col-xl-8 col-lg-8">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.note)}
                                {$payment.note}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mb-10 mt-10"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="form-group form-group-xs row">
                    <label class="col-xl-2 col-lg-2 col-form-label">
                        {__d('admin', 'ngay_tao')}
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.created)}
                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($payment.created)}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-2 col-lg-2 col-form-label">
                        {__d('admin', 'ngay_thanh_toan')}
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.payment_time)}
                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($payment.payment_time)}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group-xs row">
                    <label class="col-xl-2 col-lg-2 col-form-label">
                        {__d('admin', 'mo_ta')}
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($payment.description)}
                                {$payment.description}
                            {else}
                                ...
                            {/if}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group text-center mt-20 mb-0">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                {__d('admin', 'dong')}
            </button>

            {if !empty($payment.logs)}
                <button id="logs-payment" type="button" class="btn btn-sm btn-secondary">
                    {__d('admin', 'lich_su_thay_doi')}
                </button>
            {/if}
            
            {if isset($payment.status) && $payment.status == 2}
                <button id="confirm-payment" type="button" class="btn btn-sm btn-primary">
                    <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                    {__d('admin', 'xac_nhan_giao_dich')}
                </button>
            {/if}
        </div> 
    </div>

    <div wrap-confirm class="d-none">
        {if isset($payment.status) && $payment.status == 2}
            {$this->element('../Payment/element_confirm_payment')}

            <div class="form-group text-center mt-20 mb-0">
                <button id="info-payment" type="button" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai')}
                </button>
                
                <button id="btn-confirm-payment" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat_giao_dich')}
                </button>
            </div>
        {/if}
    </div>

    <div wrap-logs class="d-none">
        {if !empty($payment.logs)}
            {$this->element('../Payment/element_log_payment')}
        {/if}

        <div class="form-group text-center mt-20 mb-0">
            <button id="info-payment" type="button" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </button>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">
        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
    </span>
{/if}