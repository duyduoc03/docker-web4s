<div class="row">
    <div class="col-md-6 col-12">        
        <div class="form-group">
            <label>
                {__d('admin', 'ten_bang')}
                <span class="kt-font-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-table"></i>
                    </span>
                </div>
                <input id="collection-name" name="name" value="{if !empty($collection_info.name)}{$collection_info.name}{/if}" class="form-control" type="text" autocomplete="off">
            </div>
            
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'ma')}
                <span class="kt-font-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-barcode"></i>
                    </span>
                </div>
                <input id="collection-code" name="code" value="{if !empty($collection_info.code)}{$collection_info.code}{/if}" class="form-control {if !empty($collection_info.code)}disabled{/if}" type="text" autocomplete="off">
            </div>
            
        </div>
    </div>
</div>