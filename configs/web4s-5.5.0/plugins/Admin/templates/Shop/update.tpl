{assign var = url_list value = "{ADMIN_PATH}/shop"}
{assign var = url_add value = "{ADMIN_PATH}/shop/add"}
{assign var = url_edit value = "{ADMIN_PATH}/shop/update"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>

            <div class="btn-group">
                {if empty($id)}
                    <button data-link="{$url_edit}" data-update="1" id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                        <i class="la la-plus"></i>
                        {__d('admin', 'them_moi')} (F1)
                    </button>
                {else}
                    <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                        <i class="la la-edit"></i>
                        {__d('admin', 'cap_nhat')} (F1)
                    </button>
                {/if}
                
                <button type="button" class="btn btn-brand btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="kt-nav p-0">
                        <li class="kt-nav__item">
                            <span data-link="{$url_add}" class="kt-nav__link btn-save">
                                <i class="kt-nav__link-icon flaticon2-medical-records"></i>
                                <span class="kt-nav__link-text">
                                    {__d('admin', 'luu_&_them_moi')}
                                </span>
                            </span>
                        </li>

                        <li class="kt-nav__item">
                            <span data-link="{$url_list}" class="kt-nav__link btn-save">
                                <i class="kt-nav__link-icon flaticon2-hourglass-1"></i>
                                <span class="kt-nav__link-text">
                                    {__d('admin', 'luu_&_quay_lai')}
                                </span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/shop/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        {if !empty($id)}
            <div nh-anchor="thong_tin_cap_nhat" class="kt-portlet nh-portlet nh-active-hover position-relative">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'thong_tin_cap_nhat')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-xs-6">
                            
                            <div class="form-group form-group-xs row">
                                <label class="col-lg-4 col-xl-4 col-form-label">
                                    {__d('admin', 'trang_thai')}
                                </label>

                                <div class="col-lg-8 col-xl-8">
                                    {if isset($shop.status) && $shop.status == 1}
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'hoat_dong')}
                                        </span>
                                    {elseif ($shop.draft == 1) || (isset($shop.status) && $shop.status == 0)}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'ngung_hoat_dong')}
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
                                        {if !empty($shop.user_full_name)}
                                            {$shop.user_full_name}
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
                                        {if !empty($shop.created)}
                                            {$shop.created}
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
                                        {if !empty($shop.updated)}
                                            {$shop.updated}
                                        {/if}
                                    </span>
                                </div>
                            </div>

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
                            
                            {assign var = all_name_content value = $this->ShopAdmin->getAllNameContent($id)}
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

                                                            <a href="{ADMIN_PATH}/shop/update/{$shop.id}?lang={$k_language}" class="pl-10">
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
                </div>
            </div>
        {/if}        

        <div nh-anchor="thong_tin_co_ban" class="kt-portlet nh-portlet nh-active-hover position-relative">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_cua_hang')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_cua_hang')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input id="name" name="name" value="{if !empty($shop.name)}{$shop.name}{/if}" class="form-control form-control-sm" type="text" maxlength="500">
                        </div> 

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'so_dien_thoai')}
                                    </label>
                                    <input name="phone" value="{if !empty($shop.phone)}{$shop.phone}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        Hotline
                                    </label>
                                    <input name="hotline" value="{if !empty($shop.hotline)}{$shop.hotline}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        Email
                                    </label>
                                    <input name="email" value="{if !empty($shop.email)}{$shop.email}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'thoi_gian_hoat_dong')}
                                    </label>
                                    <input name="hours_operation" value="{if !empty($shop.hours_operation)}{$shop.hours_operation}{/if}" type="text" class="form-control form-control-sm" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tinh_thanh_thanh_pho')}
                                    </label>
                                    {assign var = districts value = []}
                                    {assign var = cities value = $this->LocationAdmin->getListCitiesForDropdown()}
                                    {assign var = city_id value = ''}
                                    {if !empty($shop.city_id)}
                                        {assign var = city_id value = {$shop.city_id}}
                                        {assign var = districts value = $this->LocationAdmin->getListDistrictForDropdown($shop.city_id)}
                                    {/if}
                                    {$this->Form->select('city_id', $cities, ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => $city_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'data-live-search' => true])}
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'quan_huyen')}
                                    </label>
                                    
                                    {assign var = district_id value = ''}
                                    {if !empty($shop.district_id)}
                                        {assign var = district_id value = {$shop.district_id}}
                                    {/if}
                                    {$this->Form->select('district_id', $districts, ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => $district_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'data-live-search' => true])}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'dia_chi')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </span>
                                </div>
                                <input name="address" value="{if !empty($shop.address)}{$shop.address|escape}{/if}" type="text" class="form-control form-control-sm" maxlength="500">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'duong_dan_gmap')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </span>
                                </div>
                                <input name="gmap" value="{if !empty($shop.gmap)}{$shop.gmap|escape}{/if}" type="text" class="form-control form-control-sm" maxlength="500">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'vi_tri')}
                            </label>
                            <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>
</div>