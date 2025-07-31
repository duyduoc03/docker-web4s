{assign var = website_info value = $this->Setting->getWebsiteInfo()}

<div style="width: 100%; background-color: #4c4e4e; font-size: 12px; padding-bottom: 10px; border-radius: 8px;">
    <p style="color: #fff; font-style: italic;font-size: 11px; margin: 0; line-height: 30px; text-align: center;">
        {__d('admin', 'day_la_email_tu_dong_quy_khach_vui_long_khong_tra_loi_email_nay')}
    </p>

    <div style="width: 720px; margin: 0 auto; overflow: hidden;">
        <div style="width: 700px; margin: 0 auto; overflow: hidden; background: #ececec;">
            <div style="overflow: hidden; width: 100%; margin-bottom: 20px; background-color: #3b88c8; color:#ffffff; line-height: 40px;">
                <ul style="list-style: none; margin: 0; padding: 0 20px;">
                    <li style="float: left; margin: 0;">
                        <span>
                            {__d('admin', 'ngay')}: {$smarty.now|date_format:"%d/%m/%Y"}
                        </span>
                    </li>

                    <li style="float: right; margin: 0;">
                        <span>
                            Hotline: 1900 6680 - 0901191616
                        </span>
                    </li>
                </ul>
            </div>

            <div style="overflow: hidden; width: 100%;">
                <ul style="list-style: none; margin: 0;padding: 0;">
                    <li style="float: left; width: 180px; margin: 0; padding: 0px 0 0px 20px;">
                        <img style="width: 150px;" src="{$this->Utilities->getUrlWebsite()}/admin/assets/media/logos/logo4s-01.svg">
                    </li>

                    <li style="float: right; width: 480px; margin: 0; padding: 0px 20px 0px 0px; text-align: right; font-size: 14px; line-height: 20px;">
                        <p style="margin: 0;">
                            <strong style="text-transform: uppercase;">
                                CÔNG TY TNHH PHẦN MỀM NHÂN HÒA
                            </strong>
                        </p>

                        <p style="margin: 0;">
                            Tầng 4, Tòa nhà số 97 - 99 Láng Hạ, Đống Đa, Hà Nội (Tòa nhà Petrowaco)
                        </p>
                    </li>
                </ul>
            </div>

            <div style="overflow: hidden; padding: 20px;">
                <div style="overflow: hidden; background: #fff; padding: 20px; border-radius: 5px;">
                    {$this->fetch('content')}
                </div>
            </div>

            <div style="overflow: hidden; width: 100%; background-color: #3b88c8; color:#ffffff; line-height: 40px; text-align: center;">
                Copyright © 2002 Web4s. All rights reserved.
            </div>
        </div>
    </div>
</div>