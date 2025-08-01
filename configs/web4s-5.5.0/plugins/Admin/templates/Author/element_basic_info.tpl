{if !empty($author.id)}
    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'trang_thai')}
                </label>

                <div class="col-lg-8 col-xl-8">
                    {* {if !empty($article.draft)}
                        <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'ban_luu_nhap')}
                        </span>
                    {/if} *}

                    {if isset($author.status) && $author.status == 1}
                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'hoat_dong')}
                        </span>
                    {elseif isset($author.status) && $author.status == 0}
                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'khong_hoat_dong')}
                        </span>   
                    {elseif isset($author.status) && $author.status == -1}
                        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                            {__d('admin', 'cho_duyet')}
                        </span>   
                    {/if}
                </div>
            </div>

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'tac_gia')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        {if !empty($author.full_name)}
                            {$author.full_name}
                        {/if}
                    </span>
                </div>
            </div>

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'thoi_gian_tao')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        {if !empty($author.created)}
                            {$author.created}
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
                        {if !empty($author.updated)}
                            {$author.updated}
                        {/if}
                    </span>
                </div>
            </div>

        </div>

        <div class="col-lg-6 col-xs-6">  
            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'ngon_ngu_hien_tai')}
                </label>
                <div class="col-lg-8 col-xl-8">
                    <span class="form-control-plaintext kt-font-bolder">
                        <div class="list-flags">
                            <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                            {if !empty($list_languages[$lang])}
                                {$list_languages[$lang]}
                            {/if}
                        </div>
                    </span>
                </div>
            </div>
            
            
            {if !empty($use_multiple_language) && !empty($list_languages) }
                <div class="form-group form-group-xs row">
                    <label class="col-lg-4 col-xl-4 col-form-label">
                        {__d('admin', 'sua_ban_dich')}
                    </label>
                    <div class="col-lg-12 col-xs-12">
                        <table class="table table-bordered mb-10">
                            <tbody>
                                {foreach from = $list_languages key = k_language item = language}
                                    <tr>
                                        <td class="w-90">
                                            <div class="list-flags d-inline mr-5">
                                                <img src="{ADMIN_PATH}{FLAGS_URL}{$k_language}.svg" alt="{$k_language}" class="flag" />
                                            </div>
                                            {$language}: 
                                            <i>
                                                {if !empty($author.full_name)}
                                                    {$author.full_name|truncate:100:" ..."}
                                                {else}
                                                    <span class="kt-font-danger fs-12">
                                                        {__d('admin', 'chua_nhap')}
                                                    </span>
                                                {/if}
                                            </i>

                                            <a href="{ADMIN_PATH}/author/update/{$author.id}?lang={$k_language}" class="pl-10">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>                                            
                </div>
            {/if}

            <div class="form-group form-group-xs row">
                <label class="col-lg-4 col-xl-4 col-form-label">
                    {__d('admin', 'thong_tin_tac_gia')}
                </label>

                <div class="col-lg-8 col-xl-8">
                    {if !empty($author.url)}
                        <a target="_blank" href="/{$author.url}" class="kt-link kt-font-bolder kt-link--info pt-5">
                            <i class="fa fa-external-link-alt"></i>
                            {__d('admin', 'xem_tac_gia')}
                        </a>
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-10"></div>
{/if}

<div class="row">
    <div class="col-6">
	    <div class="form-group">
	        <label>
	            {__d('admin', 'ho_va_ten')}
	            <span class="kt-font-danger">*</span>
	        </label>
	        <div class="input-group">
	            <div class="input-group-prepend">
	                <span class="input-group-text">
	                    <i class="fa fa-user-alt fs-12"></i>
	                </span>
	            </div>
	            <input name="full_name" value="{if !empty($author.full_name)}{$author.full_name}{/if}" class="form-control form-control-sm " type="text" maxlength="255">
	        </div>
	    </div>
	</div> 
	<div class="col-6">
	    <div class="form-group">
	        <label>
	            {__d('admin', 'chuc_vu')}
	        </label>
	        <div class="input-group">
	            <div class="input-group-prepend">
	                <span class="input-group-text">
	                    <i class="fa fa-user-tie fs-12"></i>
	                </span>
	            </div>
	            <input name="job_title" value="{if !empty($author.job_title)}{$author.job_title}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
	        </div>
	    </div>
	</div>                
</div>
<div class="row">
    <div class="col-lg-3 col-xl-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'so_dien_thoai')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-phone fs-12"></i>
                    </span>
                </div>
                <input name="phone" value="{if !empty($author.phone)}{$author.phone}{/if}" type="text" class="form-control form-control-sm phone-input">
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-xl-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'email')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-envelope fs-12"></i>
                    </span>
                </div>
                <input name="email" value="{if !empty($author.email)}{$author.email}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'dia_chi')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-map-marker-alt fs-12"></i>
                    </span>
                </div>
                <input name="address" value="{if !empty($author.address)}{$author.address}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        {__d('admin', 'album_anh')}
    </label>
 
    <div class="row wrap-album">
        <div class="col-xl-8 col-lg-8">
            <input id="images" name="images" value="{if !empty($author.images)}{htmlentities($author.images|@json_encode)}{/if}" type="hidden" />
            <div class="clearfix mb-5 list-image-album">
                {if !empty($author.images)}
                    {foreach from = $author.images item = image}
                    
                        <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
                            <img src="{CDN_URL}{$image}">
                            <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>
                        </a>
                    {/foreach}
                {/if}
            </div>
        </div>
        <div class="col-xl-2 col-lg-4">
            {assign var = url_select_album value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&field_id=images&lang={LANGUAGE_ADMIN}"}

            <span class="col-12 btn btn-sm btn-success btn-select-image-album" data-src="{$url_select_album}" data-type="iframe">
                <i class="fa fa-images"></i> 
                {__d('admin', 'chon_anh_album')}
            </span>
        </div>
    </div>                            
</div>
    
<div class="form-group">
    <label>
        {__d('admin', 'duong_dan_video')}
    </label>

    <div class="row wrap-video">
        <div class="col-xl-8 col-lg-8">
            <input name="url_video" id="url_video" value="{if !empty($author.url_video)}{$author.url_video}{/if}" type="text" class="form-control form-control-sm">
            <span class="form-text text-muted">
                {__d('admin', 'voi_kieu_video_youtube_url_chi_dien_ma_video')} 
                <img src="{ADMIN_PATH}/assets/media/note/upload_video.png" width="300px" />
            </span>
        </div>

        <div class="col-xl-4 col-lg-4">
            <div class="row">
                <div class="col-xl-6 col-lg-12">
                    {$this->Form->select('type_video', $this->ListConstantAdmin->listTypeVideo(), ['id' => 'type_video', 'empty' => null, 'default' => "{if !empty($author.type_video)}{$author.type_video}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker mb-10'])}
                </div>

                {assign var = url_select_video value = "{CDN_URL}/myfilemanager/?type_file=video&cross_domain=1&token={$access_key_upload}&field_id=url_video&lang={LANGUAGE_ADMIN}"}

                <div class="col-xl-6 col-lg-12">
                    <span class="col-12 btn btn-sm btn-success d-none btn-select-video" data-src="{$url_select_video}" data-type="iframe">
                        <i class="fa fa fa-photo-video"></i> 
                        {__d('admin', 'chon_video')}
                    </span>
                </div>
            </div>                                    
        </div>                                
    </div>
</div>
<div class="row">
    <div class="col-lg-2 col-xl-2 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'vi_tri')}
            </label>
            <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
        </div>
    </div>
</div>
<div class="row">   
    <div class="col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'anh_chinh')}
            </label>
            <div class="clearfix">
                {assign var = bg_avatar value = ''}
                {if !empty($author.avatar)}
                    {assign var = bg_avatar value = "background-image: url('{CDN_URL}{$author.avatar}');background-size: contain;background-position: 50% 50%;"}
                {/if}
                
                {assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=avatar&lang={LANGUAGE_ADMIN}"}

                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_avatar)}kt-avatar--changed{/if}">
                    <a {if !empty($author.avatar)}href="{CDN_URL}{$author.avatar}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_avatar}"></a>
                    <label class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-src="{$url_select_avatar}" data-type="iframe">
                        <i class="fa fa-pen"></i>
                    </label>
                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                        <i class="fa fa-times"></i>
                    </span>

                    <input id="avatar" name="avatar" value="{if !empty($author.avatar)}{htmlentities($author.avatar)}{/if}" type="hidden" />
                </div>
            </div>
        </div>
    </div>
</div>



