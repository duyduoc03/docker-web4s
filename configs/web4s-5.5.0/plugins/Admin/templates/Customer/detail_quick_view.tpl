{if !empty($customer)}
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'ho_va_ten')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.full_name)}
                            {$customer.full_name}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'ma_khach_hang')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.code)}
                            {$customer.code}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'email')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.email)}
                            {$customer.email}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'tai_khoan')}
                </label>

                <div class="col-lg-8 col-xl-9">                                
                    {if !empty($customer.username)}
                        <span class="kt-font-bolder">
                            {$customer.username}
                            
                            {if isset($customer.account_status) && $customer.account_status == 0}
                                <i class="text-danger fs-12">
                                    ({__d('admin', 'ngung_hoat_dong')})
                                </i>
                            {/if}

                            {if isset($customer.account_status) && $customer.account_status == 1}
                                <i class="text-success fs-12">
                                    ({__d('admin', 'dang_kich_hoat')})
                                </i>
                            {/if}

                            {if isset($customer.account_status) && $customer.account_status == 2}
                                <i class="text-danger fs-12">
                                    ({__d('admin', 'cho_kich_hoat')})
                                </i>
                            {/if}
                        </span>
                    {else}
                        <span class="kt-font-bolder">
                            {__d('admin', 'chua_thiet_lap')}
                        </span>
                    {/if}
                    
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'gioi_tinh')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.sex) && $customer.sex eq 'male'}
                            {__d('admin', 'nam')}
                        {elseif !empty($customer.sex) && $customer.sex eq 'female'}
                            {__d('admin', 'nu')}
                        {elseif !empty($customer.sex) && $customer.sex eq 'other'}
                            {__d('admin', 'khac')}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'so_dien_thoai')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.phone)}
                            {$customer.phone}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'dia_chi')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.full_address)}
                            {$customer.full_address}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xl-3 col-lg-4">
                    {__d('admin', 'ngay_sinh')}
                </label>

                <div class="col-lg-8 col-xl-9">
                    <span class="kt-font-bolder">
                        {if !empty($customer.birthday)}
                            {$customer.birthday}
                        {/if}
                    </span>
                </div>
            </div>   

        </div>
    </div>
{else}
    <span class="kt-datatable--error">
        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
    </span>
{/if}