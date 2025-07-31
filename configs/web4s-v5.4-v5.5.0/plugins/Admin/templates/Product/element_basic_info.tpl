<div class="form-group">
    <label>
        {__d('admin', 'ten_san_pham')}
        <span class="kt-font-danger">*</span>
    </label>
    <input id="name" name="name" value="{if !empty($product.name)}{$product.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
</div>

<div id="wrap-category" class="row">
    <div class="col-lg-9">
        <div class="form-group">
            <label>
                {__d('admin', 'danh_muc')}
                <span class="kt-font-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-align-justify w-20px"></i>
                    </span>
                </div>
                {assign var = categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                    {TYPE} => {PRODUCT}, 
                    {LANG} => $lang
                ])}

                {assign var = categories_selected value = []}
                {if !empty($product.categories)}
                    {foreach from = $product.categories item = category}
                        {$categories_selected[] = $category.id}
                    {/foreach}
                {/if}

                {$this->Form->select('categories', $categories, ['id' => 'categories', 'empty' => null, 'default' => $categories_selected, 'class' => 'form-control kt-select-multiple', 'multiple' => 'multiple', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}"])}
            </div>
        </div> 
    </div>

    <div class="col-lg-3">
        <label>
            {__d('admin', 'danh_muc_chinh')}
        </label>

        {$this->Form->select('main_category_id', $main_categories, ['id' => 'main_category_id', 'empty' => {__d('admin', 'chon')}, 'default' => $main_category_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}", 'nh-brand-by-category' => "{if !empty($brand_by_category)}1{else}0{/if}", 'nh-attribute-by-category' => "{if !empty($attribute_by_category)}1{else}0{/if}", 'nh-item-attribute-by-category' => "{if !empty($item_attribute_by_category)}1{else}0{/if}"])}
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'thuong_hieu')}
            </label>

            {assign var = brands value = $this->BrandAdmin->getBrandByMainCategory($main_category_id, $lang)}

            {assign var = search_brand value = false}
            {if !empty($brands) && count($brands) > 7}
                {$search_brand = true}                                        
            {/if}

            {$this->Form->select('brand_id', $brands, ['id' => 'brand_id', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($product.brand_id)}{$product.brand_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-live-search' => $search_brand])}
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-3">
        <div class="form-group mb-0">
            <label>
                {__d('admin', 'vi_tri')}
            </label>

            <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
        </div>
    </div>

    <div class="col-xl-3 col-lg-3">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="form-group">
                    <label>
                        {__d('admin', 'san_pham_noi_bat')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="featured" value="1" {if !empty($product.featured)}checked{/if}> {__d('admin', 'co')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="featured" value="0" {if empty($product.featured)}checked{/if}> {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="form-group">
                    <label class="mb-10">
                        {__d('admin', 'hien_thi_muc_luc')}
                    </label>

                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--tick kt-radio--success">
                            <input type="radio" name="catalogue" value="1" {if !empty($product.catalogue)}checked{/if}> 
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="catalogue" value="0" {if empty($product.catalogue)}checked{/if}> 
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-3 col-lg-3">
        <div class="form-group mb-3">
            <label class="text-uppercase">
                {__d('admin', 'Vat')}
            </label>

            <input name="vat" value="{if !empty($product.vat)}{$product.vat}{/if}" class="form-control form-control-sm" type="text">
        </div>
        
    </div>
    <div class="col-12">
        <div class="note" style="font-style: italic;">
            <span class="kt-font-danger">
                {__d('admin', 'luu_y')}:
            </span>
            <span>
                {__d('admin', 'chi_nen_nhap_thong_tin_nay_voi_nhung_san_pham_ap_dung_vat_rieng_vat_chung_he_thong_se_tu_dong_ap_dung')}
            </span>
        </div>
    </div>
</div>