{assign var = url_list value = "{ADMIN_PATH}/ladipage"}
{assign var = url_add value = "{ADMIN_PATH}/ladipage/add"}
{assign var = url_edit value = "{ADMIN_PATH}/ladipage/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit,
    'show_lang' => true
])}

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/ladipage/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="d-none">
            <input type="hidden" name="seo_score" value="" id="seo-score">
            <input type="hidden" name="keyword_score" value="" id="keyword-score">
        </div>

        <div class="row">
            <div class="col-lg-12 col-xs-12">
                {if !empty($ladipage.id)}
                    <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    {__d('admin', 'thong_tin_cap_nhat')}
                                </h3>
                            </div>

                            {if !empty($ladipage.url)}
                                <div class="kt-portlet__head-toolbar">
                                    <a target="_blank" href="/{$ladipage.url}" class="kt-link kt-font-bolder kt-link--info">
                                        {__d('admin', 'xem_bai_viet')}
                                    </a>
                                </div>
                            {/if}
                        </div>

                        <div class="kt-portlet__body pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-xs-6">
                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'trang_thai')}
                                        </label>

                                        <div class="col-lg-8 col-xl-8">
                                            {if isset($ladipage.status) && $ladipage.status == 1}
                                                <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'hoat_dong')}
                                                </span>
                                            {elseif isset($ladipage.status) && $ladipage.status == 0}
                                                <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </span>   
                                            {elseif isset($ladipage.status) && $ladipage.status == -1}
                                                <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'cho_duyet')}
                                                </span>   
                                            {/if}
                                        </div>
                                    </div>

                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'LadiPage Key')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($ladipage.ladipage_key)}
                                                    {$ladipage.ladipage_key}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xs-6">
                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'thoi_gian_tao')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($ladipage.created)}
                                                    {$ladipage.created}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'cap_nhat_moi')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($ladipage.updated)}
                                                    {$ladipage.updated}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_chinh')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="form-group">
                            <label>
                                Tên landing
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($ladipage.name)}{$ladipage.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'duong_dan')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="la la-link"></i>
                                    </span>
                                </div>
                                <input name="link" value="{if !empty($ladipage.url)}{$ladipage.url}{/if}" data-link-id="{if !empty($ladipage.url_id)}{$ladipage.url_id}{/if}" type="text" class="form-control form-control-sm nh-link" maxlength="255">
                            </div>
                        </div>             
                        
                        <div class="form-group">
                            <label>
                                {__d('admin', 'noi_dung')}
                            </label>
                            <div id="embed-code-content" class="nh-embed-code" style="height: 800px;">{if !empty($ladipage.content)}{htmlentities($ladipage.content)}{/if}</div>
                            <div class="clearfix d-none">
                                <textarea name="content" id="content" class="nh-embed-code">{if !empty($ladipage.content)}{htmlentities($ladipage.content)}{/if}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>