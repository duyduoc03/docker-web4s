{assign var = all_name_content value = $this->BrandAdmin->getAllNameContent($id)}

{if !empty($use_multiple_language) && !empty($list_languages) }
    <div class="row mb-20">
        <div class="col-lg-6 col-xs-6">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'trang_thai')}
                            </td>
                            <td class="kt-font-bolder">
                                {if isset($brand.status) && $brand.status == 1}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'hoat_dong')}
                                    </span>
                                {elseif isset($brand.status) && $brand.status == 0}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </span>   
                                {elseif isset($brand.status) && $brand.status == -1}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill">
                                        {__d('admin', 'cho_duyet')}
                                    </span>   
                                {/if}
                            </td>
                        </tr>
                        {if !empty($brand.user_full_name)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'nguoi_tao')}
                                </td>
                                <td class="kt-font-bolder">
                                    {$brand.user_full_name}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'thoi_gian_tao')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($brand.created)}
                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.created)}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'cap_nhat_moi')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($brand.updated)}
                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.updated)}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'xem_thuong_hieu')}
                            </td>
                            <td>
                                {if !empty($brand.url)}
                                    <a target="_blank" href="/{$brand.url}"  class="pt-1 kt-badge kt-badge--info kt-badge--inline kt-badge--pill pt-0 d-inline-flex align-items-center">
                                        <i class="fa fa-external-link-alt mr-5"></i>
                                        {__d('admin', 'xem_thuong_hieu')}
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-6 col-xs-6">  
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
                                    <td>
                                        <i>
                                            {if !empty($all_name_content[$k_language])}
                                                {$all_name_content[$k_language]|truncate:100:" ..."}
                                            {else}
                                                <span class="kt-font-danger fs-12">
                                                    {__d('admin', 'chua_nhap')}
                                                </span>
                                            {/if}
                                        </i>

                                        <a href="{ADMIN_PATH}/brand/update/{$brand.id}?lang={$k_language}" class="pl-10">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
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
        <div class="col-lg-6 col-xs-6">
            <div class="kt-table-update">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'trang_thai')}
                            </td>
                            <td class="kt-font-bolder">
                                {if isset($brand.status) && $brand.status == 1}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                        {__d('admin', 'hoat_dong')}
                                    </span>
                                {elseif isset($brand.status) && $brand.status == 0}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </span>   
                                {elseif isset($brand.status) && $brand.status == -1}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                        {__d('admin', 'cho_duyet')}
                                    </span>   
                                {/if}
                            </td>
                        </tr>
                        {if !empty($brand.user_full_name)}
                            <tr>
                                <td class="w-30">
                                    {__d('admin', 'nguoi_tao')}
                                </td>
                                <td class="kt-font-bolder">
                                    {$brand.user_full_name}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'thoi_gian_tao')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($brand.created)}
                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.created)}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'cap_nhat_moi')}
                            </td>
                            <td class="kt-font-bolder">
                                {if !empty($brand.updated)}
                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.updated)}
                                {/if}   
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-6 col-xs-6">  
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
                        <tr>
                            <td class="w-30">
                                {__d('admin', 'xem_thuong_hieu')}
                            </td>
                            <td>
                                {if !empty($brand.url)}
                                    <a target="_blank" href="/{$brand.url}"  class="pt-1 kt-badge kt-badge--info kt-badge--inline kt-badge--pill pt-0 d-inline-flex align-items-center">
                                        <i class="fa fa-external-link-alt mr-5"></i>
                                        {__d('admin', 'xem_thuong_hieu')}
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