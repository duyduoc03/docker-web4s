<div class="form-group">
    <label>
        {__d('admin', 'tieu_de')}
        <span class="kt-font-danger">*</span>
    </label>
    <input name="name" value="{if !empty($article.name)}{$article.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
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
                    {TYPE} => {ARTICLE}, 
                    {LANG} => $lang
                ])}

                {assign var = categories_selected value = []}
                {if !empty($article.categories)}
                    {foreach from = $article.categories item = category}
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
        {$this->Form->select('main_category_id', $list_category_main, ['id' => 'main_category_id', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($article.main_category_id)}{$article.main_category_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}", 'nh-attribute-by-category' => "{if !empty($attribute_by_category)}1{else}0{/if}"])}
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-4 col-md-4 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'tac_gia')}
            </label>
            {$this->Form->select('author_id', $this->AuthorAdmin->getListAuthorsForDropdown($lang), ['id' => 'author_id', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($article.author_id)}{$article.author_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-placeholder' => "{__d('admin', 'chon_tac_gia')}", 'data-size' => '5', 'data-live-search' => true])}
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-4 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'luot_xem')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <input name="view" value="{if !empty($article.view)}{$article.view}{/if}" type="text" class="form-control form-control-sm number-input" >
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-xl-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'vi_tri')}
            </label>
            <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
        </div>
    </div>

    <div class="col-lg-4 col-xl-3 col-12">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="mb-10">
                        {__d('admin', 'bai_noi_bat')}
                    </label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--tick kt-radio--success">
                            <input type="radio" name="featured" value="1" {if !empty($article.featured)}checked{/if}> 
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="featured" value="0" {if empty($article.featured)}checked{/if}> 
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="mb-10">
                        {__d('admin', 'hien_thi_muc_luc')}
                    </label>

                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--tick kt-radio--success">
                            <input type="radio" name="catalogue" value="1" {if !empty($article.catalogue)}checked{/if}> 
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="catalogue" value="0" {if empty($article.catalogue)}checked{/if}> 
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
