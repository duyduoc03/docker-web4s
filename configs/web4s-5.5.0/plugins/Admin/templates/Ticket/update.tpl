{assign var = url_list value = "{ADMIN_PATH}/ticket"}
{assign var = url_add value = "{ADMIN_PATH}/ticket/add"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {if !empty($url_add)}
                <div class="btn-group">
                    <button data-link="{$url_list}" id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                        <i class="la la-plus"></i>
                        {__d('admin', 'them_moi')} (F1)
                    </button>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/ticket/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ho_va_ten')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-tie"></i>
                                </span>
                            </div>
                            <input name="full_name" value="{if !empty($ticket.full_name)}{$ticket.full_name}{else if !empty($user.full_name)}{$user.full_name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'email')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>

                            <input name="email" value="{if !empty($ticket.email)}{$ticket.email}{else if !empty($user.email)}{$user.email}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'so_dien_thoai')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>

                            <input name="phone" value="{if !empty($ticket.phone)}{$ticket.phone}{else if !empty($user.phone)}{$user.phone}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'tieu_de')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-list-ul"></i>
                                </span>
                            </div>

                            <input name="title" value="{if !empty($ticket.title)}{$ticket.title}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'phong_ban')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="row">
                            <div class="col-sm-5 col-12">
                                {$this->Form->select('department', $this->ListConstantAdmin->listDepartment(), ['id'=>'department', 'empty' => {__d('admin', 'chon_phong_ban')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>

                            <div class="col-sm-7 col-12">
                                <div class="form-group row mb-0">
                                    <label class="col-lg-3 col-form-label">
                                        {__d('admin', 'muc_do')}
                                    </label>

                                    <div class="col-lg-9">
                                        {$this->Form->select('priority', $this->ListConstantAdmin->listPriority(), ['id'=>'priority', 'empty' => {__d('admin', 'chon_muc_do')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'noi_dung')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <textarea name="content" id="content" class="form-control" rows="8"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'tep_dinh_kem')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div id="files-attach" class="dropzone dropzone-multi">
                            <div class="dropzone-panel">
                                <a class="dropzone-select btn btn-label-brand btn-bold btn-sm">
                                    <i class="fa fa-cloud-upload-alt"></i>
                                    {__d('admin', 'chon_tep')}
                                </a>
                                <a class="dropzone-remove-all btn btn-label-danger btn-bold btn-sm">
                                    <i class="fa fa-trash-alt"></i>
                                    {__d('admin', 'xoa_tat_ca')}
                                </a>
                            </div>

                            <div class="dropzone-items">
                                <div class="dropzone-item" style="display:none">
                                    <div class="dropzone-file">
                                        <div class="dropzone-filename" title="">
                                            <span data-dz-name></span> 
                                            <strong>
                                                (<span data-dz-size></span>)
                                            </strong>
                                        </div>
                                        <div class="dropzone-error" data-dz-errormessage></div>
                                    </div>
                                    <div class="dropzone-progress">
                                        <div class="progress">
                                            <div class="progress-bar kt-bg-brand" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress></div>
                                        </div>
                                    </div>
                                    <div class="dropzone-toolbar">
                                        <span class="dropzone-start" style="display: none;">
                                            <i class="flaticon2-arrow"></i>
                                        </span>

                                        <span class="dropzone-cancel" data-dz-remove style="display: none;">
                                            <i class="flaticon2-cross"></i>
                                        </span>

                                        <span class="dropzone-delete" data-dz-remove>
                                            <i class="flaticon2-cross"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="form-text text-muted">
                            {__d('admin', 'dung_luong_toi_da_{0}_va_so_luong_toi_da_{1}_tep_tin', ['10Mb', '5'])}.
                        </span>

                        <input name="files" type="hidden" value="">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>