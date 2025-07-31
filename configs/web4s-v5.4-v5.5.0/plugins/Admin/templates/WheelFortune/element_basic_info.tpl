{assign var = list_language value = $this->LanguageAdmin->getList()}

{if !empty($wheel_fortune.id)}
    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'trang_thai')}
                </label>

                <div class="col-lg-8 col-xl-8">
                    {if isset($wheel_fortune.status) && $wheel_fortune.status == 1}
                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'hoat_dong')}
                        </span>
                    {elseif isset($wheel_fortune.status) && $wheel_fortune.status == 0}
                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'khong_hoat_dong')}
                        </span>   
                    {elseif isset($wheel_fortune.status) && $wheel_fortune.status == -1}
                        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'cho_duyet')}
                        </span>   
                    {/if}
                </div>
            </div>

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'nguoi_tao')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        {if !empty($wheel_fortune.user_full_name)}
                            {$wheel_fortune.user_full_name}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'thoi_gian_tao')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        {if !empty($wheel_fortune.created)}
                            {$wheel_fortune.created}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'cap_nhat_moi')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">                        
                        {if !empty($wheel_fortune.updated)}
                            {$wheel_fortune.updated}
                        {/if}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xs-6">  
            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'ngon_ngu_hien_tai')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        <div class="list-flags">
                            <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                            {if !empty($list_languages[$lang])}
                                {$list_languages[$lang]}
                            {/if}
                        </div>
                    </span>
                </div>
            </div>
            
            {assign var = all_name_content value = $this->WheelFortuneAdmin->getAllNameContent($id)}
            {if !empty($use_multiple_language) && !empty($list_languages) }
                <div class="form-group form-group-xs row">
                    <label class="col-lg-4 col-xl-4 col-form-label">
                        {__d('admin', 'sua_ban_dich')}
                    </label>
                    <div class="col-lg-12 col-xs-12">
                        <table class="table table-bordered mb-10">
                            <tbody>
                                {foreach from = $list_languages key = k_language item = language}
                                    <tr>
                                        <td class="w-90">
                                            <div class="list-flags d-inline mr-5">
                                                <img src="{ADMIN_PATH}{FLAGS_URL}{$k_language}.svg" alt="{$k_language}" class="flag" />
                                            </div>
                                            {$language}: 
                                            <i>
                                                {if !empty($all_name_content[$k_language])}
                                                    {$all_name_content[$k_language]|truncate:100:" ..."}
                                                {else}
                                                    <span class="kt-font-danger fs-12">
                                                        {__d('admin', 'chua_nhap')}
                                                    </span>
                                                {/if}
                                            </i>

                                            <a href="{ADMIN_PATH}/wheel-fortune/update/{$wheel_fortune.id}?lang={$k_language}" class="pl-10">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>                                            
                </div>
            {/if}
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-10"></div>
{/if}

<div class="form-group">
    <label>
        {__d('admin', 'ten_vong_quay_may_man')}
        <span class="kt-font-danger">*</span>
    </label>
    <input name="name" value="{if !empty($wheel_fortune.name)}{$wheel_fortune.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
</div>

<div class="row">
    <div class="col-lg-2 col-xl-2 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'co_hoi_trung_thuong')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="flaticon2-percentage"></i>
                    </span>
                </div>
                <input name="winning_chance" value="{if !empty($wheel_fortune.winning_chance)}{$wheel_fortune.winning_chance}{/if}" type="text" max="100" class="form-control form-control-sm number-input" >
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-xl-4 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'thoi_gian_ap_dung')}
            </label>
            <div class="input-daterange input-group">
                <input id="start_time" type="text" class="form-control form-control-sm kt_datepicker" name="start_time" value="{if !empty($wheel_fortune.start_time)}{$this->UtilitiesAdmin->convertIntgerToDateString($wheel_fortune.start_time)}{/if}" placeholder="{__d('admin', 'tu')}" autocomplete="off">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="la la-ellipsis-h"></i>
                    </span>
                </div>
                <input id="end_time" type="text" class="form-control form-control-sm kt_datepicker" name="end_time" value="{if !empty($wheel_fortune.end_time)}{$this->UtilitiesAdmin->convertIntgerToDateString($wheel_fortune.end_time)}{/if}" placeholder="{__d('admin', 'den')}" autocomplete="off">
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="form-group">
            <label class="pb-2">
                {__d('admin', 'gioi_han_so_luong_giai_thuong')}
            </label>
            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-2 w-100 mt-2">
                <input id="check_limit" type="checkbox" name="check_limit" {if !empty($wheel_fortune.check_limit)}checked="checked"{/if}> 
                {__d('admin', 'khi_giai_thuong_dat_den_gioi_han_no_van_hien_thi_tren_banh_xe_nhung_khong_quay_duoc')}
                <span></span>
            </label> 
        </div>
    </div>
</div>

<div class="form-group mt-30">
    <span id="add-item" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i> Thêm giải thưởng
    </span>
</div>

{$this->element("../WheelFortune/item_option")}
