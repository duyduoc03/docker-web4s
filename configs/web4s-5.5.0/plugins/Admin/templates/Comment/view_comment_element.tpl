<div class="kt-todo__head">
    <div class="kt-section__info">
        <div class="row kt-margin-b-5">
            <div class="col-lg-6 col-xs-6">
                {if !empty($comment.full_name)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'ho_va_ten')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            {if !empty($comment.full_name) && !empty($comment.admin_user_id)}
                                <a href="{ADMIN_PATH}/user/detail/{$comment.admin_user_id}" class="kt-font-bolder" target="_blank">
                                    {$comment.full_name}
                                </a>
                            {elseif !empty($comment.full_name) && !empty($comment.customer_account_id)}
                                <a href="{ADMIN_PATH}/customer/detail/{$comment.customer_account_id}" class="kt-font-bolder" target="_blank">
                                    {$comment.full_name}
                                </a>
                            {elseif !empty($comment.full_name)}
                                <div class="kt-font-bolder d-inline-block">
                                    {$comment.full_name}
                                </div>
                            {/if}
                            {if (!empty($comment.is_admin))}
                                <span class="kt-badge kt-badge--danger kt-badge--inline ml-5"> {__d('admin', 'quan_tri')}</span>
                            {/if}
                        </div>
                    </div>
                {/if}

                {if !empty($comment.email)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'email')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                {$comment.email}
                            </span>
                        </div>
                    </div>
                {/if}

                {if !empty($comment.email)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'so_dien_thoai')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                {$comment.phone}
                            </span>
                        </div>
                    </div>
                {/if}

                {if !empty($comment.ip)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'dia_chi_ip')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                {$comment.ip}
                            </span>
                        </div>
                    </div>
                {/if}
            </div>

            <div class="col-lg-6 col-xs-6">
                {if !empty($comment.status)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'trang_thai')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            {if isset($comment.status) && $comment.status eq 0}
                                <span class="kt-badge kt-badge--danger kt-badge--inline">{__d('admin', 'khong_duyet')}</span>
                            {/if}
                            {if isset($comment.status) && $comment.status eq 1}
                                <span class="kt-badge kt-badge--success kt-badge--inline">{__d('admin', 'da_duyet')}</span>
                            {/if}
                            {if isset($comment.status) && $comment.status eq 2}
                                <span class="kt-badge kt-badge--warning kt-badge--inline">{__d('admin', 'cho_duyet')}</span>
                            {/if}
                        </div>
                    </div>
                {/if}

                {if !empty($comment.full_time)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'ngay_tao')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                {$comment.full_time}
                            </span>
                        </div>
                    </div>
                {/if}

                {if !empty($comment.number_like)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'ua_thich')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                {$comment.number_like} like
                            </span>
                        </div>
                    </div>
                {/if}

                {if !empty($comment.url)}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'duong_dan')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                <a href="/{$comment.url}">{__d('admin', 'xem_chi_tiet')}</a>
                            </span>
                        </div>
                    </div>
                {/if}
                
                {if !empty($comment.type_comment) && $comment.type_comment eq 'rating'}
                    <div class="form-group form-group-xs row">
                        <div class="col-lg-4 col-xl-4 ">
                            {__d('admin', 'danh_gia')}
                        </div>
                        <div class="col-lg-8 col-xl-8">
                            <span class="kt-font-bolder">
                                <div class="star-rating">
                                    <span style="width:{math equation="x * y" x=$comment.rating|intval y=20}%"></span>
                                </div>
                            </span>
                        </div>
                    </div>
                {/if}

                {if !empty($comment.type_comment)}
                    <input id="type-comment" type="hidden" value="{$comment.type_comment}">
                {/if}
            </div>
        </div>
    </div>
</div>

<div class="kt-separator kt-separator--space-lg kt-separator--border-dashed kt-margin-t-15 kt-margin-b-15"></div>


<div class="kt-todo__body p-0 mb-4">
    <h6>{__d('admin', 'binh_luan')}:</h6>

    {if !empty($comment.content)}
        <div class="kt-todo__text scroll-comment">
            {$comment.content}
        </div>
    {/if}

    {if !empty($comment.images)}
        <div class="d-flex flex-wrap kt-margin-t-10">
            {foreach from = $comment.images item = item}
                <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" href="{CDN_URL}{$item}" data-lightbox="view-comment">
                    <img class="img-fluid img-cover" src="{CDN_URL}{$item}" alt="image" style="width: 80px">
                </a>
            {/foreach}
        </div>
    {/if}
</div>

<form id="main-form" action="{ADMIN_PATH}/comment/admin-reply" method="POST" autocomplete="off" novalidate="novalidate">
    <div class="form-group">
        <h6>{__d('admin', 'nhap_binh_luan')}:</h6>
        <textarea name="content" class="form-control" id="content" rows="6" style="resize: none;"></textarea>
    </div>
    <input id="id_comment" name="id" type="hidden" value="{if !empty($comment.id)}{$comment.id}{/if}">
    <div class="list-image-album" style="display: none"></div>
    <div class="kt-todo__primary d-flex kt-margin-t-25 kt-margin-b-25">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary btn-save">
                {__d('admin', 'binh_luan')}
            </button>
        </div>
        <div class="kt-todo__panel ml-5">
            <span class="btn btn-sm btn-primary" id="nh-trigger-upload">
                <i class="flaticon-photo-camera kt-margin-r-0"></i>
            </span>
            <input name="files[]" type="file" class="d-none nh-input-comment-images" accept="image/*" multiple="multiple">
            <input name="images" id="images" type="hidden" value="">
        </div>
    </div>
</form>