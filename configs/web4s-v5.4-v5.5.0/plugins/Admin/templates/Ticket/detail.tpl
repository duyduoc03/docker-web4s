{assign var = url_list value = "{ADMIN_PATH}/ticket"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {*if empty($ticket) || (!empty($ticket.status) && !in_array($ticket.status, [{RESOLVED}, {CLOSED}]))}
                <a href="javascript:;" class="btn btn-sm btn-secondary">
                    {__d('admin', 'da_xu_ly')}
                </a>
            {/if*}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-3 col-md-4 col-12">
            <div class="kt-portlet nh-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <i class="fa fa-ticket-alt mr-2"></i>
                            {__d('admin', 'thong_tin_ticket')}
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-widget kt-widget--user-profile-2 kt-widget-ticket">
                        <div class="kt-widget__body">
                            <div class="kt-widget__item">
                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'ma_ticket')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.code)}
                                            #{$ticket.code}
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'tieu_de')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.title)}
                                            {$ticket.title}
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'trang_thai')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.status) && $ticket.status == {NEW_TICKET}}
                                            <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'moi')}
                                            </span>
                                        {else if !empty($ticket.status) && $ticket.status == {ASSIGNED}}
                                            <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'tiep_nhan')}
                                            </span>
                                        {else if !empty($ticket.status) && $ticket.status == {IN_PROGRESS}}
                                            <span class="kt-badge kt-badge--brand kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'dang_xu_ly')}
                                            </span>
                                        {else if !empty($ticket.status) && $ticket.status == {WAITING_CUSTOMER}}
                                            <span class="kt-badge kt-badge--warning kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'gui_khach_hang')}
                                            </span>
                                        {else if !empty($ticket.status) && $ticket.status == {RESOLVED}}
                                            <span class="kt-badge kt-badge--brand kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'da_xu_ly')}
                                            </span>
                                        {else if !empty($ticket.status) && $ticket.status == {CLOSED}}
                                            <span class="kt-badge kt-badge--success kt-font-bold kt-badge--inline kt-badge--pill">
                                                {__d('admin', 'dong_ticket')}
                                            </span>
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'phong_ban')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.department_name)}
                                            {$ticket.department_name}
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'muc_do')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.priority) && $ticket.priority == {LOW}}
                                            <span class="kt-badge kt-badge--primary kt-badge--dot"></span>
                                            &nbsp;
                                            <span class="kt-font-bold kt-font-primary">
                                                {__d('admin', 'thap')}
                                            </span>
                                        {else if !empty($ticket.priority) && $ticket.priority == {MEDIUM}}
                                            <span class="kt-badge kt-badge--warning kt-badge--dot"></span>
                                            &nbsp;
                                            <span class="kt-font-bold kt-font-warning">
                                                {__d('admin', 'trung_binh')}
                                            </span>
                                        {else if !empty($ticket.priority) && $ticket.priority == {HIGH}}
                                            <span class="kt-badge kt-badge--danger kt-badge--dot"></span>
                                            &nbsp;
                                            <span class="kt-font-bold kt-font-danger">
                                                {__d('admin', 'cao')}
                                            </span>
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'ngay_tao')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.created)}
                                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($ticket.created)}
                                        {/if}
                                    </span>
                                </div>

                                <div class="kt-widget__contact mb-10">
                                    <span class="kt-widget__label">
                                        {__d('admin', 'cap_nhat_lan_cuoi')}:
                                    </span>
                                    <span class="kt-widget__data">
                                        {if !empty($ticket.updated)}
                                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($ticket.updated)}
                                        {else if !empty($ticket.created)}
                                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($ticket.created)}
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-md-8 col-12">
            <form id="main-form" action="{ADMIN_PATH}/ticket/reply{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_chinh')}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="kt-portlet__body">
                        <div class="kt-widget31 mb-20">
                            <div class="kt-widget31__item">
                                <div class="kt-widget31__content">
                                    <div class="kt-widget31__pic">
                                        <span class="kt-badge kt-badge--username kt-badge--unified-warning kt-badge--lg kt-badge--rounded kt-badge--boldest">
                                            {if !empty($auth_user.full_name)}
                                                {mb_substr($auth_user.full_name, 0, 1, 'UTF-8')}
                                            {/if}
                                        </span>
                                    </div>
                                    <div class="kt-widget31__info">
                                        <div class="kt-widget31__username">
                                            {if !empty($ticket.full_name)}
                                                {$ticket.full_name}
                                            {/if}
                                        </div>
                                        <p class="kt-widget31__text">
                                            {if !empty($ticket.email)}
                                                <span class="mr-20">
                                                    <i class="flaticon2-new-email"></i>
                                                    {$ticket.email}
                                                </span>
                                            {/if}

                                            {if !empty($ticket.phone)}
                                                <span>
                                                    <i class="fa fa-phone"></i>
                                                    {$ticket.phone}
                                                </span>
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                                <div class="kt-widget31__content justify-content-end">
                                    <span class="btn-outline-brand btn btn-sm btn-bold" data-toggle="collapse" data-target="#collapseReply" aria-expanded="false" aria-controls="collapseReply">
                                        {__d('admin', 'tra_loi')}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div id="collapseReply" class="collapse">
                            <div class="row">
                                <div class="col-sm-4 col-12">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'ho_va_ten')}
                                            <span class="kt-font-danger">*</span>
                                        </label>

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

                                <div class="col-sm-4 col-12">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'email')}
                                            <span class="kt-font-danger">*</span>
                                        </label>

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

                                <div class="col-sm-4 col-12">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'so_dien_thoai')}
                                        </label>

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
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'noi_dung')}
                                </label>

                                <textarea name="content" id="content" class="form-control" rows="8"></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tep_dinh_kem')}
                                </label>

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
                                <span class="form-text text-muted mt-5">
                                    {__d('admin', 'dung_luong_toi_da_{0}_va_so_luong_toi_da_{1}_tep_tin', ['10Mb', '5'])}.
                                </span>

                                <input name="files" type="hidden" value="">
                            </div>

                            <div class="form-group">
                                <span class="btn btn-outline-secondary btn btn-sm btn-bold" data-toggle="collapse" data-target="#collapseReply" aria-expanded="false" aria-controls="collapseReply">
                                    {__d('admin', 'dong')}
                                </span>

                                <span class="btn btn-sm btn-success btn-save">
                                    {__d('admin', 'cap_nhat')}
                                </span>
                            </div>
                        </div>

                        <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>

                        <div class="kt-notes">
                            <div class="kt-notes__items">
                                {if !empty($logs_ticket)}
                                    {foreach from=$logs_ticket item=item key=key}
                                        <div class="kt-notes__item">
                                            <div class="kt-notes__media">
                                                <span class="kt-badge kt-avatar-fullname kt-badge--username kt-badge--unified-warning kt-badge--lg kt-badge--rounded kt-badge--boldest">
                                                    {if !empty($item.crm_staff_name)}
                                                        {mb_substr($item.crm_staff_name, 0, 1, 'UTF-8')}
                                                    {else if empty($item.crm_staff_name) && !empty($item.full_name)}
                                                        {mb_substr($item.full_name, 0, 1, 'UTF-8')}
                                                    {/if}
                                                </span>
                                            </div>
                                            <div class="kt-notes__content">
                                                <div class="kt-notes__section">
                                                    <div class="kt-notes__info">
                                                        <span class="kt-notes__title">
                                                            {if !empty($item.crm_staff_name)}
                                                                {$item.crm_staff_name}
                                                            {else if empty($item.crm_staff_name) && !empty($item.full_name)}
                                                                {$item.full_name}
                                                            {/if}
                                                        </span>
                                                        <span class="kt-notes__desc">
                                                            {if !empty($item.created)}
                                                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($item.created)}
                                                            {/if}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="kt-notes__body">
                                                    {if !empty($item.content)}
                                                        {$item.content|escape:'html'|nl2br}
                                                    {/if}
                                                </span>

                                                {if !empty($item.files)}
                                                    <div class="clearfix list-files-ticket">
                                                        {foreach from = $item.files item = file}
                                                            {if empty($file.url)}{continue}{/if}

                                                            <a href="{CDN_URL}{$file.url}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative">
                                                                {if !empty($file.extension) && ($file.extension == 'xlsx' || $file.extension == 'xls')}
                                                                    <i class="fa fa-file-excel"></i>
                                                                {elseif !empty($file.extension) && ($file.extension == 'docx' || $file.extension == 'doc')}
                                                                    <i class="fa fa-file-word"></i>
                                                                {elseif !empty($file.extension) && $file.extension == 'pdf'}
                                                                    <i class="fa fa-file-pdf"></i>
                                                                {else}
                                                                    <img src="{CDN_URL}{$file.url}">
                                                                {/if}
                                                            </a>
                                                        {/foreach}
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>