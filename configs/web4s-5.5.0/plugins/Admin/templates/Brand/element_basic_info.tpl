<div class="form-group">
    <label>
        {__d('admin', 'ten_thuong_hieu')}
        <span class="kt-font-danger">*</span>
    </label>
    <input name="name" value="{if !empty($brand.name)}{$brand.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
</div>

<div class="row">
    <div class="col-xl-2 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'vi_tri')}
            </label>
            <input name="position" value="{if !empty($position)}{$position}{/if}" class="form-control form-control-sm" type="text">
        </div>
    </div>
</div>
