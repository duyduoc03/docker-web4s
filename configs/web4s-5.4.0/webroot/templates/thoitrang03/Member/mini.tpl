{assign member_info value = $this->Member->getMemberInfo()}
{if !empty($member_info.avatar)}
    {assign var = member_img value = "{CDN_URL}{$member_info.avatar}"}
{else}
    {assign var = member_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
{/if}


<div class="dropdown show">
    <a class="text-white align-items-center d-flex" href="javascript:;" role="button" id="member-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <img class="img-fluid rounded-circle" src="{$member_img}" alt="{if !empty($member_info.full_name)}{$member_info.full_name}{/if}" title="{if !empty($member_info.full_name)}{$member_info.full_name}{/if}"  style="width: 3rem;">
        
        {*if !empty($member_info.full_name)}
            <div class="acc-show ml-2">
                <span>
                    {__d('template', 'tai_khoan')}
                </span>
                <strong>
                    {$member_info.full_name}
                </strong>
            </div>
        {/if*}
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="member-info">
        <a class="dropdown-item py-3" href="/member/dashboard">
            <i class="fa-light fa-user mr-3"></i>
            {__d('template', 'thong_tin_ca_nhan')}
        </a>
        
        <a class="dropdown-item py-3" href="/member/order">
            <i class="fa-light fa-clipboard-list-check mr-3"></i>
            {__d('template', 'quan_ly_don_hang')}
        </a>
        
        <a class="dropdown-item py-3" href="/member/change-password">
            <i class="fa-light fa-lock-keyhole mr-3"></i>
            {__d('template', 'thay_doi_mat_khau')}
        </a>

        <a class="dropdown-item py-3" href="/member/logout">
            <i class="fa-light fa-right-from-bracket mr-3"></i>
            {__d('template', 'thoat')}
        </a>
    </div>
</div>