<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'facebook')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Facebook App ID
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-facebook"></i>
                                    </span>
                                </div>
                                <input name="facebook_app_id" value="{if !empty($social.facebook_app_id)}{$social.facebook_app_id}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', '{0}_su_dung_de_cau_hinh_ham_khoi_tao_cho_cac_ung_dung_cua_facebook_nhung_vao_website', ['Facebook App ID'])}
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Facebook secret
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input name="facebook_secret" value="{if !empty($social.facebook_secret)}{$social.facebook_secret}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'google')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Google Client ID
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-google"></i>
                                    </span>
                                </div>
                                <input name="google_client_id" value="{if !empty($social.google_client_id)}{$social.google_client_id}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Google secret
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input name="google_secret" value="{if !empty($social.google_secret)}{$social.google_secret}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'Apple')}
                    </h3>
                </div>
            </div>


            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Client ID
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-apple"></i>
                                    </span>
                                </div>
                                <input name="apple_client_id" value="{if !empty($social.apple_client_id)}{$social.apple_client_id}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Apple secret
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input name="apple_secret" value="{if !empty($social.apple_secret)}{$social.apple_secret}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
