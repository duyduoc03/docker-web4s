{assign var = url_list value = "{ADMIN_PATH}/mobile-app/block"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'cap_nhat_block')}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>            
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-config-form" action="{ADMIN_PATH}/mobile-app/block/save-main-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-file-alt mr-5"></i>
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group form-group row">
                    <label class="col-xl-1 col-lg-2 col-form-label">
                        {__d('admin', 'loai_block')}:
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {assign var = type value = "{if !empty($block_info.type)}{$block_info.type}{/if}"}
                            {assign var = list_type_block value = $this->MobileTemplateAdmin->getListTypeMobileBlock()}

                            {if !empty($type) && !empty($list_type_block[$type])}
                                {$list_type_block[$type]}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-lg-9 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_block')}
                                <span class="kt-font-danger">*</span>
                            </label>

                            <input name="name" value="{if !empty($block_info.name)}{$block_info.name}{/if}" class="form-control form-control-sm" type="text">
                        </div>                        

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'trang_thai')}
                                    </label>

                                    <div class="kt-radio-inline mt-5">
                                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                            <input type="radio" name="status" value="1" {if !empty($block_info.status)}checked="true"{/if}> 
                                                {__d('admin', 'hoat_dong')}
                                            <span></span>
                                        </label>

                                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                            <input type="radio" name="status" value="0" {if empty($block_info.status)}checked="true"{/if}> 
                                                {__d('admin', 'ngung_hoat_dong')}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>

                <div class="d-none">
                    <input id="type-block" type="hidden" value="{if !empty($block_info.type)}{$block_info.type}{/if}">
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-main-config-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </div>
    </form>

    {if !empty($type)}
        <div id="wrap-block-config" data-code="{if !empty($code)}{$code}{/if}" class="clearfix">
            {$this->element("../MobileTemplateBlock/element_config_{$type}", ['config' => $config])}
        </div>
    {/if}
</div>


<div id="add-element-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Element
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form action="{ADMIN_PATH}/mobile-app/block/add-element/{if !empty($code)}{$code}{/if}" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-file-alt"></i>
                                </span>
                            </div>
                            <input id="element-name" value="" type="text" class="form-control form-control-sm required">
                        </div> 
                    </div>

                    <div class="form-group mb-0">
                        <label>
                            {__d('admin', 'ma')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </span>
                            </div>
                            <input id="element-code" value="" type="text" class="form-control form-control-sm required">
                        </div>
                        <span class="form-text text-muted">
                            {__d('admin', 'ma_thuoc_tinh_viet_thuong_va_khong_chua_ky_tu_dac_biet')}
                        </span>
                    </div>
                </form>                 
            </div>

            <div class="modal-footer">
                <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </span>
                
                <span nh-btn="add-element" class="btn btn-sm btn-brand">
                    <i class="fa fa-check"></i>
                    {__d('admin', 'them_moi')}
                </span>
            </div>
        </div>
    </div>
</div>

<div id="add-style-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Style View
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form action="{ADMIN_PATH}/mobile-app/block/add-style-view/{if !empty($code)}{$code}{/if}" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-file-alt"></i>
                                </span>
                            </div>
                            <input id="view-name" value="" type="text" class="form-control form-control-sm required">
                        </div> 
                    </div>

                    <div class="form-group mb-0">
                        <label>
                            {__d('admin', 'ma')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </span>
                            </div>
                            <input id="view-code" value="" type="text" class="form-control form-control-sm required">
                        </div>
                        <span class="form-text text-muted">
                            {__d('admin', 'ma_thuoc_tinh_viet_thuong_va_khong_chua_ky_tu_dac_biet')}
                        </span>
                    </div>
                </form>                 
            </div>

            <div class="modal-footer">
                <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </span>
                
                <span nh-btn="add-view" class="btn btn-sm btn-brand">
                    <i class="fa fa-check"></i>
                    {__d('admin', 'them_moi')}
                </span>
            </div>
        </div>
    </div>
</div>

<div id="get-action-redirect-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_chuyen_huong')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form action="{ADMIN_PATH}/mobile-app/block/get-action-redirect" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'Loại điều hướng')}
                        </label>
                       
                        <select name="type" id="select-type" class="form-control form-control-sm kt-selectpicker">
                            <option value="" selected="selected">-- {__d('admin', 'chon')} --</option>
                            <option name ="redirect" value="redirect" > {__d('admin', 'Redirect')} </option>
                            <option name ="form" value="form"> {__d('admin', 'Form')} </option>
                        </select>
                    </div>
                    
                    <div nh-wrap="forms" class="form-group d-none">
                        <label>
                            {__d('admin', 'Form')}
                        </label>
                         {assign var = forms value = $this->ContactFormAdmin->getAllFormForDropdown()}
                        <select name="form" id="select-form" class="form-control form-control-sm kt-selectpicker">
                            <option value="" selected="selected">-- {__d('admin', 'chon')} --</option>
                            {if !empty($forms)}
                                {foreach from = $forms item = form_info}
                                    <option  value="{if !empty($form_info.code)}{$form_info.code}{/if}">
                                        {if !empty($form_info.name)}
                                            {$form_info.name}
                                        {/if}
                                    </option>
                                {/foreach}
                            {/if}
                        </select>
                    </div> 
                    <div nh-wrap="pages" class="d-none">
                        <div  class="form-group" >
                            <label>
                                {__d('admin', 'trang')}
                            </label>
                            {assign var = pages value = $this->MobileTemplateAdmin->getAllPage()}

                            <select name="page" id="select-page" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">-- {__d('admin', 'chon')} --</option>
                                {if !empty($pages)}
                                    {foreach from = $pages item = page_info}
                                        <option value="{if !empty($page_info.type)}{$page_info.type}{/if}" >
                                            {if !empty($page_info.name)}
                                                {$page_info.name}
                                            {/if}
                                        </option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                                                         
                        <div nh-wrap="category_product" class="form-group d-none">
                            <label>
                                {__d('admin', 'danh_muc_san_pham')}
                            </label>
                            {assign var = categories_product value = $this->CategoryAdmin->getListCategoriesForDropdown([
                                {TYPE} => PRODUCT, 
                                {LANG} => $lang
                            ])}
                            {$this->Form->select('category_product_id', $categories_product, ['empty' => "-- {__d('admin', 'chon')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>

                        <div nh-wrap="category_article" class="form-group d-none">
                            <label>
                                {__d('admin', 'danh_muc_bai_viet')}
                            </label>
                            {assign var = categories_product value = $this->CategoryAdmin->getListCategoriesForDropdown([
                                {TYPE} => ARTICLE, 
                                {LANG} => $lang
                            ])}
                            {$this->Form->select('category_article_id', $categories_product, ['empty' => "-- {__d('admin', 'chon')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>

                        <div nh-wrap="product_detail" class="clearfix d-none">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chon_san_pham')}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="flaticon-search"></i>
                                        </span>
                                    </div>
                                    <input id="product-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_san_pham')}" autocomplete="off">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'san_pham_da_chon')}
                                </label>
                                <div nh-wrap="product-selected" class="clearfix mh-35 tagify">
                                    {if !empty($data)}
                                        {foreach from = $data item = product_id}
                                            {assign var = product_info value = $this->ProductAdmin->getDetailProduct($product_id, $lang)}
                                            <span class="tagify__tag">
                                                <x class="tagify__tag__removeBtn" role="button"></x>
                                                <div>
                                                    <span class="tagify__tag-text">
                                                        {if !empty($product_info.name)}
                                                            {$product_info.name}
                                                        {/if}
                                                    </span>
                                                </div>
                                                <input name="data_ids[]" value="{if !empty($product_info.id)}{$product_info.id}{/if}" type="hidden">
                                            </span>
                                        {/foreach}
                                    {/if}
                                </div>        
                            </div>
                        </div>                    

                        <div nh-wrap="article_detail" class="clearfix d-none">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chon_bai_viet')}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="flaticon-search"></i>
                                        </span>
                                    </div>
                                    <input id="article-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_bai_viet')}" autocomplete="off">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'bai_viet_da_chon')}
                                </label>
                                <div nh-wrap="article-selected" class="clearfix mh-35 tagify">                                
                                    {if !empty($data)}
                                        {foreach from = $data item = article_id}
                                            {assign var = item_info value = $this->ArticleAdmin->getDetailArticle($article_id, $lang)}
                                            <span class="tagify__tag">
                                                <x class="tagify__tag__removeBtn" role="button"></x>
                                                <div>
                                                    <span class="tagify__tag-text">
                                                        {if !empty($item_info.name)}
                                                            {$item_info.name}
                                                        {/if}
                                                    </span>
                                                </div>
                                                <input name="data_ids[]" value="{if !empty($item_info.id)}{$item_info.id}{/if}" type="hidden">
                                            </span>
                                        {/foreach}
                                    {/if}
                                </div>        
                            </div>
                        </div>

                        <input name="page_type" type="hidden" value="">
                    </div>
                    <div class="form-group d-none" nh-wrap="resultInput">
                        <label>
                                {__d('admin', 'Action mobile')}
                        </label>
                        <div class="input-group">
                            
                            
                            <input data-type-action="" action="copy" name="resultInput" value="" class="form-control form-control-sm " type="text">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-brand  btn-copy" type="button">Copy</button>
                            </div>
                        </div>  
                    </div>
                           
                </form>
            </div>

            <div class="modal-footer">
                <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </span>
                
                <span nh-btn="get-action-redirect" action="copy" class="btn btn-sm btn-brand" data-dismiss="">
                    <i class="fa fa-check"></i>
                    {__d('admin', 'lay_cau_hinh')}
                </span>
            </div>
        </div>
    </div>
</div>

