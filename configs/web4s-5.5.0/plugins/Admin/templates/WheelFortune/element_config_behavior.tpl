<div class="row">
	<div class="col-12 col-md-6">
		<div class="form-group">
            <label>
                {__d('admin', 'nhap_thong_tin_lien_he')}
                <span class="kt-font-danger">*</span>
            </label>
            <select name="config_behavior[show_contact]" id="config_behavior" class="form-control form-control-sm kt-selectpicker">
            	<option value="1" {if !empty($wheel_fortune.config_behavior.show_contact)}selected{/if}>{__d('admin', 'truoc_khi_quay_thuong')}</option>
            	<option value="0" {if empty($wheel_fortune.config_behavior.show_contact)}selected{/if}>{__d('admin', 'sau_khi_quay_thuong')}</option>
            </select>
        </div>

		<div class="form-group">
            <label>
                {__d('admin', 'gioi_han_luot_choi')}
            </label>
            <input id="play_limit" name="config_behavior[play_limit]" value="{if !empty($wheel_fortune.config_behavior.play_limit)}{$wheel_fortune.config_behavior.play_limit}{/if}" class="form-control form-control-sm number-input" max="100" type="text" placeholder="{__d('admin', 'gioi_han_luot_choi')}">
        </div>
	</div>

	<div class="col-12 col-lg-12">
    	<div class="form-group d-inline-block">
            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-2 w-100 mt-2">
                <input nh-config="behavior" type="checkbox" name="config_behavior[retries]" {if !empty($wheel_fortune.config_behavior.retries)}checked{/if}> 
                {__d('admin', 'choi_lai')}
                <span></span>
            </label> 
        </div>
    </div>

    <div class="col-12 col-lg-6">
	    <div config-behavior class="collapse {if !empty($wheel_fortune.config_behavior.retries)}show{/if}">
	        <div class="row">
	        	<div class="col-12 col-md-6">
	        		<div class="form-group">
	                    <label>
	                        {__d('admin', 'thoi_gian_choi_lai')}
	                        <span class="kt-font-danger">*</span>
	                    </label>
	                    <select name="config_behavior[occurance_after]" id="config_behavior" class="form-control form-control-sm kt-selectpicker">
	                    	<option value="0" {if !empty($wheel_fortune.config_behavior.occurance_after)}selected{/if}>{__d('admin', 'ngay_lap_tuc')}</option>
	                    	<option value="1" {if !empty($wheel_fortune.config_behavior.occurance_after)}selected{/if}>{__d('admin', 'sau_khoang_thoi_gian')}</option>
	                    </select>
	                </div>
	        	</div>

	        	<div class="col-12 col-md-6">
	        		<div class="form-group">
	                    <label>
	                        {__d('admin', 'choi_lai_sau')}
	                        <span class="kt-font-danger">*</span>
	                    </label>
	                    <div class="input-group">
	                    	<div class="input-group-prepend">
			                    <span class="input-group-text w-auto">
			                        {__d('admin', 'ngay')}
			                    </span>
			                </div>
		                    <input id="occurance_after_delay" name="config_behavior[occurance_after_delay]" value="{if !empty($wheel_fortune.config_behavior.occurance_after_delay)}{$wheel_fortune.config_behavior.occurance_after_delay}{/if}" class="form-control form-control-sm number-input" type="text">
	                    </div>
	                </div>
	        	</div>
	        </div>
	    </div>
    </div>
</div>