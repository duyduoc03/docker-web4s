<form id="payment-confirm-form" action="{ADMIN_PATH}/payment/confirm{if !empty($payment.id)}/{$payment.id}{/if}" method="POST" autocomplete="off">                        
    <div class="form-group">
        <label>
            {__d('admin', 'so_tien_da_chuyen')}
        </label>

        <input name="amount" value="{if !empty($payment.amount)}{$payment.amount|floatval}{/if}" class="form-control form-control-sm number-input text-right" type="text">
    </div>

    <div class="form-group">
        <label>                                        
            {__d('admin', 'ma_tham_chieu')}
        </label>
        <input name="reference" class="form-control form-control-sm" type="text" value="">
    </div>

    <label>
        {__d('admin', 'trang_thai')}
    </label> 

    <div class="form-group">
        <div class="kt-form__control">
            {$this->Form->select('status', $this->PaymentAdmin->getListStatus(), ['id'=>'status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => "{if !empty($payment.status)}{$payment.status}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker required', 'autocomplete' => 'off'])}
        </div>
    </div> 

    <div class="form-group">
        <label>
            {__d('admin', 'ghi_chu')}
        </label>

        <textarea id="note" name="note" rows="4" class="form-control"></textarea>
    </div>
</form>