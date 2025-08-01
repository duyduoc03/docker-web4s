{assign member_info value = $this->Member->getMemberInfo()}
{if !empty($member_info.avatar)}
    {assign var = avatar value = "{CDN_URL}{$this->Utilities->getThumbs($member_info.avatar, 150)}"}
{else}
    {assign var = avatar value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
{/if}
<div class="dropdown show">
    <a class="full_name_cs" href="javascript:;" role="button" id="member-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="d-flex align-items-center">
            <img class="rounded-circle header_avt" nh-avatar src="{$avatar}" alt="{$member_info.full_name}"/>
            <span class="user_full_name">
                {$member_info.full_name|truncate:15:'...':true:true}
            </span>
        </div>
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