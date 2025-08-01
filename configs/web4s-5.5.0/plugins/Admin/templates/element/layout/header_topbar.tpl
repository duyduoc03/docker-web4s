<div class="kt-header__topbar">
    <div class="kt-header__topbar-item kt-header__topbar-item--search" data-toggle="kt-tooltip" title="{__d('admin', 'trang_chu')}" data-placement="bottom">
        <div class="kt-header__topbar-wrapper" data-offset="10px,0px">
            <a href="/" target="_blank" class="kt-header__topbar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                        <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                    </g>
                </svg>
            </a>
        </div>
    </div>
    
    <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" data-toggle="kt-tooltip" title="{__d('admin', 'tim_kiem')}" data-placement="bottom" icon-search>
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
            <span class="kt-header__topbar-icon" btn-search>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                    </g>
                </svg> 
            </span>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg dropdown-search-all">
            <div nh-wrap="quick-search" class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact">
                <div class="kt-quick-search__form">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="flaticon2-search-1"></i>
                            </span>
                        </div>
                        <input nh-input="quick-search" type="text" class="form-control kt-quick-search__input" placeholder="{__d('admin', 'tim_kiem')}...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-close kt-quick-search__close"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div nh-wrap="result-search" class="kt-quick-search__wrapper kt-scroll" data-scroll="true" data-height="325" data-mobile-height="200"></div>
            </div>
        </div>
    </div>

    {assign var = last_time value = $this->NhNotificationAdmin->getLastTimeNotification()}
    <div class="kt-header__topbar-item kt-header__topbar-item--quick-panel" data-toggle="kt-tooltip" title="{__d('admin', 'thong_bao')}" data-placement="bottom">
        <span nh-notification="mini" id="kt_quick_panel_toggler_btn" class="kt-header__topbar-icon">
            <span nh-notification="count-notification" data-last-time="{$last_time}" class="kt-badge kt-badge--outline kt-badge--danger mini-notification d-none">
                <span class="text-danger"></span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"/>
                    <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"/>
                </g>
            </svg>
        </span>
    </div>

    <div class="kt-header__topbar-item kt-header__topbar-item--user kt-margin-l-10">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
            <div class="kt-header__topbar-user">
                <span class="kt-header__topbar-welcome kt-hidden-mobile">
                    {__d('admin', 'xin_chao')},
                </span>
                <span class="kt-header__topbar-username kt-hidden-mobile">
                    {if !empty($auth_user.full_name)}
                        {preg_replace("/\s.*$/","", $auth_user.full_name)}
                    {/if}
                </span>
                {* <img class="kt-hidden" alt="Pic" src="{ADMIN_PATH}/assets/media/users/300_25.jpg" /> *}

                <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                <span class="kt-badge kt-badge--username kt-badge--unified-warning kt-badge--lg kt-badge--rounded kt-badge--boldest">
                    {if !empty($auth_user.full_name)}
                        {mb_substr($auth_user.full_name, 0, 1, 'UTF-8')}
                    {/if}
                </span>
            </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-md dropdown-info-user">

            <!--begin: Head -->
            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x p-3" style="background: #282f48 !important; background-image: url({ADMIN_PATH}/assets/media/misc/bg-1.jpg)">
                <div class="kt-user-card__avatar">
                    {* <img class="kt-hidden" alt="Pic" src="{ADMIN_PATH}/assets/media/users/300_25.jpg" /> *}

                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-primary">
                        {if !empty($auth_user.full_name)}
                            {mb_substr($auth_user.full_name, 0, 1, 'UTF-8')}
                        {/if}
                    </span>
                </div>
                <div class="kt-user-card__name">
                    {if !empty($auth_user.full_name)}
                        {$auth_user.full_name}
                    {/if}
                </div>
            </div>

            <!--end: Head -->

            <!--begin: Navigation -->
            <div class="kt-notification">
                <a href="{ADMIN_PATH}/user/profile" class="kt-notification__item p-3">
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-user-alt kt-font-primary fs-14"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'thong_tin_tai_khoan')}
                        </div>
                    </div>
                </a>

                <a href="{ADMIN_PATH}/user/profile-change-password" class="kt-notification__item p-3">
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-unlock-alt kt-font-primary fs-14"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'thay_doi_mat_khau')}
                        </div>
                    </div>
                </a>

                <a href="{ADMIN_PATH}/user/language-admin" class="kt-notification__item p-3">
                    
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-language kt-font-primary fs-14"></i>
                    </div>
                    
                    <div class="kt-notification__item-details kt-notification__item-details-lang">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'ngon_ngu_quan_tri')}
                        </div>
                    </div>

                </a>

                <div class="kt-notification__custom kt-space-between border-top-0 p-3">
                    <a href="{ADMIN_PATH}/logout" class="btn btn-label btn-label-danger btn-sm btn-bold">
                        <i class="fa fa-sign-out-alt"></i>
                        
                        {__d('admin', 'dang_xuat')}
                    </a>
                </div>
            </div>

            <!--end: Navigation -->
        </div>
    </div>

</div>