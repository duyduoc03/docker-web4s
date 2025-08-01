{assign var = user_info value = []}
{if !empty($id_record)}
    {$user_info = $this->User->getDetailUser($id_record)}
{/if}

{if !empty($user_info)}
    <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
        {__d('admin', 'xac_nhan_thay_doi_mat_khau')}
    </div>

    {if !empty($token)}
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            <p>{__d('admin', 'ma_xac_nhan')}</p>
            <h2>
                <strong>
                    {$token}
                </strong>
            </h2>
        </div>
    {/if}

    <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
        {__d('admin', 'de_xac_nhan_viec_thay_doi_mat_khau_nay_la_hop_le_ban_vui_long_truy_cap_vao')}
        <a href="{$this->Utilities->getUrlWebsite()}/admin/verify-forgot-password{if !empty($user_info.email)}?email={$user_info.email}{/if}" target="_blank">
            <strong>{__d('admin', 'duong_dan')}</strong>
        </a>
    </div>

    {if !empty($user_info.username)}
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            {__d('admin', 'neu_nhu_ban_khong_thay_doi_mat_khau_voi_tai_khoan_{0}_vui_long_bo_qua_va_xoa_email_nay', [$user_info.username])}.
        </div>
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            {__d('admin', 'xin_cam_on')}!
        </div>
    {/if}
{/if}