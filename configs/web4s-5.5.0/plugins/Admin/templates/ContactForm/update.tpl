{assign var = url_list value = "{ADMIN_PATH}/contact/form"}
{assign var = url_add value = "{ADMIN_PATH}/contact/form/add"}
{assign var = url_edit value = "{ADMIN_PATH}/contact/form/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

{assign var = message value = $this->Utilities->getParamsByKey('message')}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/contact/form/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_form')}
                                 <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($form.name)}{$form.name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_form')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-qrcode"></i>
                                    </span>
                                </div>
                                <input name="code" value="{if !empty($form.code)}{$form.code}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'ma_form_se_duoc_su_dung_de_nhung_ngoai_website')}
                            </span>
                        </div>
                    </div>
                </div>                        

                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label class="mb-10">
                                {__d('admin', 'nhan_email_khi_lien_he')}
                            </label>

                            {assign var = list_email_templates value = $this->EmailTemplateAdmin->getListEmailTemplates()}
                            {$this->Form->select('template_email_code', $list_email_templates, ['id' => 'template_email_code', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($form.template_email_code)}{$form.template_email_code}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3">
                        <div class="form-group">
                            <label class="mb-15">
                                {__d('admin', 'gui_email')}
                            </label>
                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="send_email" value="1" {if !empty($form.send_email)}checked{/if}> {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="send_email" value="0" {if empty($form.send_email)}checked{/if}> {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {if !empty($addons[{GOOGLE_SHEET}])}
                        <div class="col-xl-3 col-lg-3">
                            <div class="form-group">
                                <label class="mb-15">
                                    {__d('admin', 'gui_thong_tin_lien_he_qua_google_sheet')}
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success">
                                        <input type="radio" name="google_sheet_status" value="1" {if !empty($form.google_sheet_status)}checked{/if}> {__d('admin', 'co')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--danger">
                                        <input type="radio" name="google_sheet_status" value="0" {if empty($form.google_sheet_status)}checked{/if}> {__d('admin', 'khong')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>

            </div>
        </div>

        {if !empty($addons[{GOOGLE_SHEET}])}
            <div class="kt-portlet nh-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'cau_hinh_google_sheet')}
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="row">

                        {assign config_form value = ''}
                        {if !empty($form.google_sheet_config)}
                            {assign config_form value = $form.google_sheet_config|json_decode:true}
                        {/if}

                        <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                                {if !empty($google_sheet_config.email)}
                                    <label class="mb-10">
                                        {__d('admin', 'email_uy_quyen')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input name="email" value="{if !empty($google_sheet_config.email)}{$google_sheet_config.email}{/if}" class="form-control form-control-sm" type="text" maxlength="255" {if !empty($google_sheet_config.email)}readonly{/if}>
                                    </div>
                                {else}
                                    <a href="javascript://" class="btn btn-primary" btn-oauth-google-sheet>
                                        {__d('admin', 'thiet_lap_thong_tin_uy_quyen')}
                                    </a>
                                {/if}

                                <div class="form-text text-muted mb-3">
                                    {__d('admin', 'thiet_lap_thong_tin_uy_quyen_cho_phep_ung_dung_cap_quyen_vao_google_sheet')}
                                    <a href="javascript://" data-toggle="modal" data-target="#modal-instruct-google-sheet">{__d('admin', 'huong_dan_cau_hinh')}</a>
                                </div>

                                {if !empty($google_sheet_config.email)}
                                    <a href="javascript://" class="btn btn-sm btn-primary" btn-deauthorize-email data-id="{if !empty($id)}{$id}{/if}">
                                        {__d('admin', 'huy_uy_quyen')}
                                    </a>
                                {/if}
                            </div>
                        </div>

                        {if !empty($google_sheet_config.email)}
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group mb-0">
                                    <label class="mb-10">
                                        {__d('admin', 'ma_bang_tinh')}
                                         <span class="kt-font-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-qrcode"></i>
                                            </span>
                                        </div>

                                        {assign spreadsheet_id value = ''}
                                        {if !empty($google_sheet_config.email) && !empty($config_form.email) && !empty($config_form.spreadsheet_id) && $google_sheet_config.email == $config_form.email}
                                            {assign spreadsheet_id value = $config_form.spreadsheet_id}
                                        {/if}
                                        <input name="spreadsheet_id" value="{$spreadsheet_id}" class="form-control form-control-sm" type="text" {if !empty($spreadsheet_id)}readonly{/if}>

                                        {if empty($spreadsheet_id)}
                                            <div class="input-group-append">
                                                <a href="javascript://" class="input-group-text w-auto btn-primary" btn-config-spreadsheet data-id="{if !empty($id)}{$id}{/if}">
                                                    {__d('admin', 'cau_hinh')}
                                                </a>
                                            </div>
                                        {/if}
                                    </div>

                                    {if !empty($message)}
                                        <small class="text-danger">
                                            {$message}
                                        </small>
                                    {/if}

                                    <div class="form-text text-muted mb-3">
                                        {__d('admin', 'ma_bang_tinh_se_duoc_su_dung_de_them_thong_tin_lien_he_tren_google_sheet')}. <a href="javascript://" data-toggle="modal" data-target="#modal-support-spreadsheet">{__d('admin', 'huong_dan_lay_ma_bang_tinh')}</a>
                                    </div>

                                    {if !empty($spreadsheet_id)}
                                        <a href="https://docs.google.com/spreadsheets/d/{$spreadsheet_id}/edit?gid=0#gid=0" target="_blank" class="btn btn-sm btn-primary">
                                            {__d('admin', 'xem_file')}
                                        </a>

                                        <a href="javascript://" target="_blank" cancel-google-sheet data-id="{if !empty($id)}{$id}{/if}" class="btn btn-sm btn-danger">
                                            {__d('admin', 'xoa_cau_hinh_bang_tinh')}
                                        </a>
                                    {/if}
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                
                            </div>
                        {/if}
                    </div>                
                </div>
            </div>
        {/if}

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_cac_truong_thong_tin')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div id="wrap-list-field">
                    <div data-repeater-list="fields">
                        {if !empty($form.fields)}
                            {foreach from = $form.fields key = key item = field}
                                {$this->element('../ContactForm/item_field', ['field' => $field])}
                            {/foreach}
                        {else}
                            {$this->element('../ContactForm/item_field')}
                        {/if}
                    </div>

                    <div class="row">
                        <div class="col-xl-2 col-lg-2">
                            <span data-repeater-create class="btn btn-sm btn-brand">
                                <i class="la la-plus"></i>
                                {__d('admin', 'them_truong_moi')}
                            </span>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </form>
</div>

<div id="modal-support-spreadsheet" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{__d('admin', 'huong_dan_lay_ma_bang_tinh')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>{__d('admin', 'buoc_1')}</b>
                    {__d('admin', 'ban_dang_nhap_vao_tai_khoan_gmail')} <a href="https://www.google.com/intl/vi/gmail/about/" target="_blank"><b>https://www.google.com/intl/vi/gmail/about/</b></a>
                </p>
                <p>
                    <b>{__d('admin', 'buoc_2')}</b> {__d('admin', 'truy_cap_vao_duong_dan')} <a href="https://sheets.google.com/" target="_blank"><b>https://sheets.google.com/</b></a>
                </p>
                <p>
                    {__d('admin', 'chon_bieu_tuong_dau_cong_goc_tren_ben_trai_de_tao_trang_tinh')}
                </p>
                <p>
                    <img src="{ADMIN_PATH}/assets/media/note/tao_trang_tinh.png" class="img-fluid border" />
                </p>
                <p>
                    <b>{__d('admin', 'buoc_3')}</b> {__d('admin', 'sau_khi_chon_man_hinh_chuyen_huong_den_bang_tinh_ban_vua_tao_ban_lay_ma_bang_tinh_theo_huong_dan_duoi_day_hoac_copy_chuoi_ky_tu_nhu_anh')}
                </p>
                <p>
                    {__d('admin', 'duong_dan_trang_tinh')}: https://docs.google.com/spreadsheets/d/<b>{__d('admin', 'ma_bang_tinh')}</b>/edit?gid=0#gid=0
                </p>
                <p>
                    <img src="{ADMIN_PATH}/assets/media/note/lay_id_bang_tinh.png" class="img-fluid border" />
                </p>
            </div>
        </div>
    </div>

</div>
<div id="modal-instruct-google-sheet" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{__d('admin', 'huong_dan_lay_ma_bang_tinh')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <b>{__d('admin', 'buoc_1')}</b>
                    {__d('admin', 'dang_nhap_tai_khoan_google_va_truy_cap')}
                     <a href="https://console.cloud.google.com/projectcreate?previousPage=%2Fapis%2Fdashboard%3FangularJsUrl%3D%26pli%3D1%26project%3Dsnappy-rainfall-308003&folder=&organizationId=0" target="_blank"><b>API & Services</b></a>
                     {__d('admin', 'nhap_day_du_thong_tin_de_dang_ky_tao_project_moi')}

                    <div>
                        {__d('admin', 'sau_khi_tao_thanh_cong_google_chuyen_huong_ve_man_hinh_quan_ly_ban_chon_enable_apis_and_services_de_tiep_tuc_cau_hinh')}
                    </div>
                </div>

                <div class="mb-3">
                    <b>{__d('admin', 'buoc_2')}</b> {__d('admin', 'tim_kiem_google_sheet_api_chon_enable_kich_hoat_api')}
                    <div>
                        <img src="{ADMIN_PATH}/assets/media/note/enable_api.png" class="img-fluid border mt-2" />
                        <img src="{ADMIN_PATH}/assets/media/note/gg_sheet_api.png" class="img-fluid border mt-3" />
                    </div>
                </div>

                <div class="mb-3">
                    <b>{__d('admin', 'buoc_3')}</b> {__d('admin', 'tao_ung_dung_uy_quyen_truy_cap_du_lieu_google_sheet')}
                    <div>
                        {__d('admin', 'ban_tru_cap_duong_dan')} <a href="https://console.cloud.google.com/apis/credentials">credentials</a> {__d('admin', 'chon_create_credentials_oauth_client_id')}
                    </div>
                    <div>
                        <img src="{ADMIN_PATH}/assets/media/note/tao_project.png" class="img-fluid border my-2" />
                    </div>
                    <div>
                        {__d('admin', 'tai_man_hinh_hien_thi_ban_chon_web_application_nhap_ten_va_authorised_redirect_uris_va_click_create')}
                        {__d('admin', 'voi_authorised_redirect_uris_ban_vui_long_nhap_theo_cau_truc_sau')} <b>{__d('admin', 'ten_mien')}</b>/admin/contact/google-auth-return
                        {__d('admin', 'khoi_tao_thanh_cong_ban_copy_ma_client_id_va_secret_vao_cau_hinh_google_mang_xa_hoi_qua_duong_dan')} <a href="/admin/setting/social">{__('admin', 'duong_dan')}</a>

                        <img src="{ADMIN_PATH}/assets/media/note/oauth_client.png" class="img-fluid border mt-3" />
                    </div>
                </div>

                <div class="mb-3">
                    <b>{__d('admin', 'buoc_4')}</b> {__d('admin', 'thiet_lap_thong_tin_uy_quyen')}
                    <div>
                        {__d('admin', 'ban_chon_form_cau_hinh_google_sheet_click_thiet_lap_thong_tin_uy_quyen_chon_email_va_lam_theo_cac_buoc_duoi_day')}
                        <img src="{ADMIN_PATH}/assets/media/note/uy_quyen.png" class="img-fluid border mt-3" />
                        <img src="{ADMIN_PATH}/assets/media/note/chon_email_uy_quyen.png" class="img-fluid border mt-3" />
                        <img src="{ADMIN_PATH}/assets/media/note/xac_minh.png" class="img-fluid border mt-3" />
                        <img src="{ADMIN_PATH}/assets/media/note/xac_minh.png" class="img-fluid border mt-3" />
                        <img src="{ADMIN_PATH}/assets/media/note/continue.png" class="img-fluid border my-3" />
                    </div>
                    <div>
                        {__d('admin', 'luu_y_tai_man_hinh_cap_quyen_ban_vui_long_tich_chon_nhu_anh_de_cap_quyen_truy_cap_google_sheet_va_chon_tiep_tuc_de_cau_hinh')}
                        <img src="{ADMIN_PATH}/assets/media/note/cap_quyen_tru_cap.png" class="img-fluid border mt-3" />
                    </div>

                    <div>
                        {__d('admin', 'neu_ban_muon_huy_thiet_lap_cau_hinh_chon_huy_uy_quyen_trong_form_sau_khi_da_thiet_lap_thanh_cong')}
                        <img src="{ADMIN_PATH}/assets/media/note/cancel.png" class="img-fluid border mt-3" />
                    </div>
                </div>
                    

                <b>{__d('admin', 'buoc_5')}</b> {__d('admin', 'xem_huong_dan_lay_ma_bang_tinh_va_dien_vao_o_ma_bang_tinh_de_cau_hinh_day_du_lieu_khach_hang_lien_he_len_google_sheet')}
            </div>
        </div>
    </div>
</div>
