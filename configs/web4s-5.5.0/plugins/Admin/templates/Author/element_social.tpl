{assign var = social value = []}
        {if !empty($author.social)}
            {$social = $author.social}
        {/if}

<div class="kt-form">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>
                    {__d('admin', 'Facebook')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fab fa-facebook-square" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <input name="social[facebook]" value="{if !empty($social['facebook'])}{$social['facebook']}{/if}" class="form-control" type="text">
                </div>
            </div>

            <div class="form-group">
                <label>
                    {__d('admin', 'Instagram')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </span>
                    </div>

                    <input name="social[instagram]" value="{if !empty($social['instagram'])}{$social['instagram']}{/if}" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>
                    {__d('admin', 'Youtube')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </span>
                    </div>

                    <input name="social[youtube]" value="{if !empty($social['youtube'])}{$social['youtube']}{/if}" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>
                    {__d('admin', 'Tiktok')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <img src="/admin/assets/media/icons/tiktok_icon.webp" alt="tiktok" style="width:20px;height:20px;">
                        </span>
                    </div>

                    <input name="social[tiktok]" value="{if !empty($social['tiktok'])}{$social['tiktok']}{/if}" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>
                    {__d('admin', 'Twitter')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="height:39px;">
                            <img src="/admin/assets/media/icons/twitter_icon.webp" alt="twitter" style="width:25px;height:25px;margin-left: -5px;">
                        </span>
                    </div>

                    <input name="social[twitter]" value="{if !empty($social['twitter'])}{$social['twitter']}{/if}" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>
                    {__d('admin', 'Linkedin')}
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            {* <img src="/admin/assets/media/icons/twitter_icon.webp" alt="twitter" style="width:20px;height:20px;"> *}
                            <i class="fab socicon-linkedin" aria-hidden="true"></i>
                        </span>
                    </div>

                    <input name="social[linkedin]" value="{if !empty($social['linkedin'])}{$social['linkedin']}{/if}" class="form-control" type="text">
                </div>
            </div> 
        </div>

        <div class="col-lg-6 col-md-6">
            <label>
                {__d('admin', 'mang_xa_hoi_khac')}
            </label>
            <div class="wrap-social-others">
                {if !empty($social['others'])}
                    {foreach $social['others'] key = index item = item_social}
                        <div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item">
                            <div class="kt-portlet__head p-5 ui-sortable-handle mh-40">
                                <div class="kt-portlet__head-label ml-5">
                                    <h3 class="kt-portlet__head-title">
                                        {if !empty($item_social.name)}
                                            {$item_social.name}
                                        {/if}
                                    </h3>
                                </div>

                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-group">
                                        <span nh-btn="delete-item-social" class="btn btn-sm btn-icon btn-danger btn-icon-md m-0">
                                            <i class="la la-trash-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-portlet__body p-10">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <div class="form-group mb-0">
                                            <label>
                                                {__d('admin', 'ten')}
                                                <span class="kt-font-danger"></span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-text-width fs-12"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="social[others_name][]" data-name="code" value="{if !empty($item_social.name)}{$item_social.name}{/if}" class="form-control form-control-sm " autocomplete="off">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-xl-8">
                                        <div class="form-group mb-0">
                                            <label>
                                                {__d('admin', 'duong_dan')}
                                                <span class="kt-font-danger"></span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-link fs-12"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="social[others_url][]" data-name="name" value="{if !empty($item_social.url)}{$item_social.url}{/if}" class="form-control form-control-sm name-field" autocomplete="off">
                                            </div>                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {else}
                    <div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item">
                        <div class="kt-portlet__head p-5 ui-sortable-handle mh-40">
                            <div class="kt-portlet__head-label ml-5">
                                <h3 class="kt-portlet__head-title">
                                </h3>
                            </div>

                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-group">
                                    <span nh-btn="delete-item-social" class="btn btn-sm btn-icon btn-danger btn-icon-md m-0">
                                        <i class="la la-trash-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="kt-portlet__body p-10">
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="form-group mb-0">
                                        <label>
                                            {__d('admin', 'ten')}
                                            <span class="kt-font-danger"></span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-text-width fs-12"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="social[others_name][]" data-name="code" value="" class="form-control form-control-sm " autocomplete="off">
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                    <div class="form-group mb-0">
                                        <label>
                                            {__d('admin', 'duong_dan')}
                                            <span class="kt-font-danger"></span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-link fs-12"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="social[others_url][]" data-name="name" value="" class="form-control form-control-sm name-field" autocomplete="off">
                                        </div>                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <span id="add-new-social" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                    {__d('admin', 'them_moi')}
                </span>
            </div>
        </div>
        
    </div>    
</div>     
