{assign var = languages value = $this->LanguageAdmin->getList()}
{assign var = lang_default value = $this->LanguageAdmin->getDefaultLanguage()}

{assign var = normal_data_extend value = []}
{if !empty($block_info.normal_data_extend)}
    {$normal_data_extend = $block_info.normal_data_extend|json_decode:1}
{/if}

<div id="wrap-block-config" data-code="{if !empty($code)}{$code}{/if}" class="clearfix">
    <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-primary" role="tablist">
        <li class="nav-item">
            <a href="#tab-general" class="nav-link active" data-toggle="tab" role="tab">
                <i class="fa fa-cogs"></i> 
                {__d('admin', 'cau_hinh_chung')}
            </a>
        </li>

        {if !empty($addons[{INTERFACE_CONFIGURATION}])}
            <li class="nav-item">
                <a href="#tab-data-extend" class="nav-link" data-toggle="tab" role="tab">
                    <i class="fa fa-database"></i> 
                    {__d('admin', 'du_lieu_mo_rong')}
                </a>
            </li>
        {/if}

        {if $type != {HTML} && !empty($addons[{INTERFACE_EDIT}])}
            <li class="nav-item">
                <a href="#tab-modify-view" class="nav-link" data-toggle="tab" role="tab">
                    <i class="fa fa-edit"></i> 
                    {__d('admin', 'sua_giao_dien')}
                </a>
            </li>
        {/if}

        <li class="nav-item d-none">
            <a href="#tab-logs" class="nav-link" data-toggle="tab" role="tab">
                <i class="fa fa-history"></i>
                {__d('admin', 'lich_su_cap_nhat')}
            </a>
        </li>

        {if !empty($block_info.id)}
            <li class="nav-item">
                <a nh-btn="show-log-tab" data-toggle="tab" href="#tab-log" role="tab" class="nav-link">
                    <i class="fa fa-history"></i>
                    {__d('admin', 'lich_su_cap_nhat')} 
                </a>
            </li>
        {/if}
    </ul>

    <div class="tab-content">
        {$this->element("../TemplateBlock/tab_general")}

        {if !empty($addons[{INTERFACE_CONFIGURATION}])}
            {$this->element("../TemplateBlock/tab_data_extend")}
        {/if}

        {if !empty($addons[{INTERFACE_EDIT}])}
            {$this->element("../TemplateBlock/tab_modify_view")}
        {/if}

        <div id="tab-logs" class="tab-pane" role="tabpanel">
            <div nh-wrap="logs" class="kt-list-timeline">
                
            </div>
        </div>

        <div id="tab-log" nh-tab="log-tab" record-id="{if !empty($block_info.id)}{$block_info.id}{/if}" sub-type="{BLOCK}" class="tab-pane" role="tabpanel"></div>
    </div>
</div>

<div class="d-none">
    <input id="type-block" type="hidden" value="{if !empty($block_info.type)}{$block_info.type}{/if}">
</div>
                
<div id="data-example-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'du_lieu_mo_rong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
{*                 <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu dữ liệu đa ngôn ngữ (locale):
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "locale": {
        "vi": {
            "tieu_de_01": "Tiêu đề 01",
            "tieu_de_02": "Tiêu đề 02"
        },
        "en": {
            "tieu_de_01": "Title 01",
            "tieu_de_02": "Title đề 02"
        },
    }
}
</pre>
                    </div>
                </div> *}

                <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu cấu hình slider (Owl Carousel 2):
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "slider": {
        "slidesPerView": 2,
        "spaceBetween": 10,
        "watchSlidesProgress": true,
        "autoplay": {
            "delay": 3000,
            "disableOnInteraction": true,
            "pauseOnMouseEnter": true
        },
        "navigation": {
            "nextEl": ".swiper-button-next",
            "prevEl": ".swiper-button-prev"
        },
        "pagination": {
            "el": ".swiper-pagination",
            "clickable": true
        },
        "breakpoints": {
            "320": {
                "grid": {
                    "rows": 2
                },
                "slidesPerView": 2,
                "spaceBetween": 15
            },
            "640": {
                "grid": {
                    "rows": 2
                },
                "slidesPerView": 2,
                "spaceBetween": 10
            },
            "768": {
                "grid": {
                    "rows": 2
                },
                "slidesPerView": 2,
                "spaceBetween": 20
            },
            "1024": {
                "grid": {
                    "rows": 2
                },
                "slidesPerView": 4,
                "spaceBetween": 30
            }
        }
    }
}

</pre>
                    </div>
                </div>

                <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu dữ liệu menu tuỳ biến:
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "locale": {
        "vi": {
            "data_sub_menu": [
                {
                    "name": "Giới thiệu",
                    "url": "/ve-chung-toi",
                    "children": [
                        {
                            "name": "Về chúng tôi",
                            "url": "/ve-chung-toi"
                        },
                        {
                            "name": "Địa chỉ",
                            "url": "/dia-chi"
                        },
                        {
                            "name": "Tầm nhìn - Sứ mệnh",
                            "url": "/tam-nhin-su-menh"
                        },
                        {
                            "name": "Tải Profile",
                            "url": "/profile"
                        }
                    ]
                }
            ]
        },
        "en": {
            "data_sub_menu": [
                {
                    "name": "Giới thiệu",
                    "url": "/ve-chung-toi",
                    "children": [
                        {
                            "name": "Về chúng tôi",
                            "url": "/ve-chung-toi"
                        },
                        {
                            "name": "Địa chỉ",
                            "url": "/dia-chi"
                        },
                        {
                            "name": "Tầm nhìn - Sứ mệnh",
                            "url": "/tam-nhin-su-menh"
                        },
                        {
                            "name": "Tải Profile",
                            "url": "/profile"
                        }
                    ]
                }
            ]
        }
    }
}
</pre>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<div id="data-collection-example-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'du_lieu_mo_rong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="kt-section">
                    <span class="kt-section__info">
                        Lưu ý dạng dữ liệu theo ngôn ngữ: 
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
    {literal}
    {if !empty($data_extend.data_collection[{LANGUAGE}])}{foreach from = $data_extend.data_collection[{LANGUAGE}] key = key item = item}{/foreach}{/if}"
    {/literal}
</pre>
                    </div>
                </div>

                <div class="kt-section">
                    <span class="kt-section__info">
                        Lưu ý dạng dữ liệu images:
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
    {literal}
        {assign var = images value = []}
            if !empty($album.images)}
                {$images = $album.images|json_decode:1}
            {/if}
    {/literal}
</pre>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
