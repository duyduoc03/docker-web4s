{strip}<div class="h3 text-uppercase font-weight-bold mb-5">
    {$this->Block->getLocale('tieu_de', $data_extend)}
</div>
<div class="row justify-content-center ">
    <div class="col-12 col-lg-8">
        <div class="bg-white p-5 rounded border mb-3">
            <p class="font-weight-bold text-uppercase">
                {$this->Block->getLocale('gui_yeu_cau', $data_extend)}
            </p>
            <form nh-form-contact="3HZO5VFWIK" action="/contact/send-info" method="POST" autocomplete="off">
                <div class="form-group">
                    <input name="full_name" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="{__d('template', 'ho_va_ten')}">
                </div>
                    
                <div class="form-group">
                    <input name="phone" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" data-rule-phoneVN data-msg-phoneVN="{__d('template', 'so_dien_thoai_chua_chinh_xac')}" placeholder="{__d('template', 'so_dien_thoai')}">
                </div>
                
                <div class="form-group">
                    <input name="title" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="{__d('template', 'tieu_de')}">
                </div>
                
                <div class="form-group">
                    <textarea name="content" maxlength="500" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="{__d('template', 'noi_dung')}"></textarea>
                </div>
                
                <div class="form-group">
                    <span nh-btn-action="submit" class="btn btn-submit">
                        {__d('template', 'gui_tin_nhan')}
                    </span>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-12 col-lg-4">
        {assign website_info value = $this->Setting->getWebsiteInfo()}
        <div class="bg-highlight text-white p-5 rounded shadow">
            <p class="font-weight-bold text-uppercase">
                {$this->Block->getLocale('ban_can_gap_truc_tiep', $data_extend)}
            </p>
            <address class="mb-0">
                {if !empty($website_info.address)}
                    <p>
                        {$this->Block->getLocale('dia_chi', $data_extend)}: {$website_info.address}
                    </p>
                {/if}
                
                {if !empty($website_info.hotline)}
                    <p>
                        {$this->Block->getLocale('so_dien_thoai', $data_extend)}: {$website_info.hotline}
                    </p>
                {/if}
                
                {if !empty($website_info.email)}
                    <p class="mb-0">
                        Email: {$website_info.email}
                    </p>
                {/if}
            </address>
        </div>
    </div>
</div>{/strip}