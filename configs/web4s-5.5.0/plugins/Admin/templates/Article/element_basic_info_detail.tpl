{assign var = all_name_content value = $this->ArticleAdmin->getAllNameContent($id)}

{if !empty($use_multiple_language) && !empty($list_languages) }
    <div class="row mb-20">
        
        <div class="col-lg-4 col-xs-4">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'trang_thai')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.draft)}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'ban_luu_nhap')}
                                    </span>
                                {/if}

                                {if isset($article.status) && $article.status == 1}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'hoat_dong')}
                                    </span>
                                {elseif isset($article.status) && $article.status == 0}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </span>   
                                {elseif isset($article.status) && $article.status == -1}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'cho_duyet')}
                                    </span>   
                                {/if}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-30">
                                {__d('admin', 'danh_gia')}
                            </td>
                            <td>
                                {if !empty($article.rating)}
                                    {$article.rating}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'luot_danh_gia')}
                            </td>
                            <td>
                                {if !empty($article.rating_number)}
                                    {$article.rating_number}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-30">
                                {__d('admin', 'luot_thich')}
                            </td>
                            <td>
                                {if !empty($article.like)}
                                    {$article.like}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>

                        {if !empty($use_multiple_language)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'xem_bai_viet')}
                                </td>
                                <td>
                                    {if !empty($article.url)}
                                        <a target="_blank" href="/{$article.url}" class="pt-1 kt-badge kt-badge--info kt-badge--inline kt-badge--pill pt-0 d-inline-flex align-items-center">
                                            <i class="fa fa-external-link-alt mr-5"></i>
                                            {__d('admin', 'xem_bai_viet')}
                                        </a>
                                    {/if}
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4 col-xs-4">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        
                        {if !empty($article.user_full_name)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'nguoi_tao')}
                                </td>
                                <td class="kt-font-bolder">
                                    {$article.user_full_name}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'thoi_gian_tao')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.created)}
                                    {$article.created}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'cap_nhat_moi')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.updated)}
                                    {$article.updated}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'seo')}
                            </td>
                            <td>
                                {if !empty($article.seo_score) && $article.seo_score == 'success'}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'tot')}
                                    </span>
                                {elseif !empty($article.seo_score) && $article.seo_score == 'warning'}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'binh_thuong')}
                                    </span>
                                {elseif !empty($article.seo_score) && $article.seo_score == 'danger'}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'chua_dat')}
                                    </span>
                                {else}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        <em>{__d('admin', 'chua_co')}</em>
                                    </span>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'tu_khoa')}
                            </td>
                            <td>
                                {if !empty($article.keyword_score) && $article.keyword_score == 'success'}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'tot')}
                                    </span>
                                {elseif !empty($article.keyword_score) && $article.keyword_score == 'warning'}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'binh_thuong')}
                                    </span>
                                {elseif !empty($article.keyword_score) && $article.keyword_score == 'danger'}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'chua_dat')}
                                    </span>
                                {else}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        <em>{__d('admin', 'chua_co')}</em>
                                    </span>
                                {/if}
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4 col-xs-4">  
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'ngon_ngu_hien_tai')}
                            </td>
                            <td>
                                <div class="list-flags  kt-font-bolder">
                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                                    {if !empty($list_languages[$lang])}
                                        {$list_languages[$lang]}
                                    {/if}
                                </div>
                            </td>
                        </tr>
                        {if !empty($use_multiple_language) && !empty($list_languages) }
                            {foreach from = $list_languages key = k_language item = language}
                                <tr>
                                    <td class="w-30">
                                        <div class="list-flags d-inline mr-5">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_language}.svg" alt="{$k_language}" class="flag" />
                                        </div>
                                        {$language}: 
                                        
                                    </td>
                                    <td class="name-language-content">
                                        <span>
                                            <i class="cursor-pointer" {if !empty($all_name_content[$k_language])}data-toggle="kt-tooltip" data-original-title="{$all_name_content[$k_language]|truncate:100:" ..."}"{/if}>
                                                {if !empty($all_name_content[$k_language])}
                                                    {$all_name_content[$k_language]|truncate:100:" ..."}
                                                {else}
                                                    <span class="kt-font-danger fs-12">
                                                        {__d('admin', 'chua_nhap')}
                                                    </span>
                                                {/if}
                                            </i>

                                            <a href="{ADMIN_PATH}/article/update/{$article.id}?lang={$k_language}" class="pl-10">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{else}
    <div class="row mb-20">
        <div class="col-lg-4 col-xs-4">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'trang_thai')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.draft)}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'ban_luu_nhap')}
                                    </span>
                                {/if}

                                {if isset($article.status) && $article.status == 1}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill ">
                                        {__d('admin', 'hoat_dong')}
                                    </span>
                                {elseif isset($article.status) && $article.status == 0}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </span>   
                                {elseif isset($article.status) && $article.status == -1}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'cho_duyet')}
                                    </span>   
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'danh_gia')}
                            </td>
                            <td>
                                {if !empty($article.rating)}
                                    {$article.rating}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'luot_danh_gia')}
                            </td>
                            <td>
                                {if !empty($article.rating_number)}
                                    {$article.rating_number}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-30">
                                {__d('admin', 'luot_thich')}
                            </td>
                            <td>
                                {if !empty($article.like)}
                                    {$article.like}
                                {else}
                                    <span class="kt-font-danger">
                                        {__d('admin', 'chua_co')}
                                    </span>
                                {/if}
                            </td>
                        </tr>

                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-4 col-xs-4">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        
                        {if !empty($article.user_full_name) && empty($article.updated)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'nguoi_tao')}
                                </td>
                                <td class="kt-font-bolder">
                                    {$article.user_full_name}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'thoi_gian_tao')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.created)}
                                    {$article.created}
                                {/if}
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'cap_nhat_moi')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($article.updated)}
                                    {$article.updated}
                                {/if}   
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'seo')}
                            </td>
                            <td>
                                {if !empty($article.seo_score) && $article.seo_score == 'success'}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'tot')}
                                    </span>
                                {elseif !empty($article.seo_score) && $article.seo_score == 'warning'}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'binh_thuong')}
                                    </span>
                                {elseif !empty($article.seo_score) && $article.seo_score == 'danger'}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'chua_dat')}
                                    </span>
                                {else}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        <em>{__d('admin', 'chua_co')}</em>
                                    </span>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'tu_khoa')}
                            </td>
                            <td>
                                {if !empty($article.keyword_score) && $article.keyword_score == 'success'}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'tot')}
                                    </span>
                                {elseif !empty($article.keyword_score) && $article.keyword_score == 'warning'}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'binh_thuong')}
                                    </span>
                                {elseif !empty($article.keyword_score) && $article.keyword_score == 'danger'}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'chua_dat')}
                                    </span>
                                {else}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
                                        <em>{__d('admin', 'chua_co')}</em>
                                    </span>
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4 col-xs-4">  
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'ngon_ngu_hien_tai')}
                            </td>
                            <td>
                                <div class="list-flags  kt-font-bolder">
                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                                    {if !empty($list_languages[$lang])}
                                        {$list_languages[$lang]}
                                    {/if}
                                </div>
                            </td>
                        </tr>
                        {if !empty($article.user_full_name) && !empty($article.updated)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'nguoi_tao')}
                                </td>
                                <td class="kt-font-bolder">
                                    {$article.user_full_name}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'xem_bai_viet')}
                            </td>
                            <td>
                                {if !empty($article.url)}
                                    <a target="_blank" href="/{$article.url}" class="pt-1 kt-badge kt-badge--info kt-badge--inline kt-badge--pill pt-0 d-inline-flex align-items-center">
                                        <i class="fa fa-external-link-alt mr-5"></i>
                                        {__d('admin', 'xem_bai_viet')}
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}