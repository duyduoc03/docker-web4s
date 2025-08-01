
<div class="form-group">
    <label>
        {__d('admin', 'ten_danh_muc')}
        <span class="kt-font-danger">*</span>
    </label>

    <input name="name" value="{if !empty($category.name)}{$category.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
</div>

<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'danh_muc_cha')}
            </label>
            {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                {TYPE} => $type, 
                {LANG} => $lang,
                {NOT_ID} => "{if !empty($category.id)}{$category.id}{/if}"
            ])}
            {$this->Form->select('parent_id', $list_categories, ['empty' => "-- {__d('admin', 'chon')} --", 'default' => "{if !empty($category.parent_id)}{$category.parent_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-3">
        <div class="form-group mb-0">
            <label>
                {__d('admin', 'vi_tri')}
            </label>
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
                </div>
            </div>
        </div>
    </div>
</div>
