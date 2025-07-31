<div class="row">
	<div class="col-12 col-lg-12">
        <div class="form-group">
            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-2 w-100 mt-2">
                <input id="config-ip" type="checkbox" name="check_ip" {if !empty($wheel_fortune.check_ip)}checked{/if}> 
                {__d('admin', 'kiem_tra_dia_chi_ip')}
                <span></span>
            </label> 
        </div>
    </div>

    <div class="col-12 col-lg-12">
    	<div class="form-group d-inline-block">
            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-2 w-100 mt-2">
                <input nh-config="email" type="checkbox" name="config_email[receive]" {if !empty($wheel_fortune.config_email.receive)}checked{/if}> 
                {__d('admin', 'nhan_email_khi_co_nguoi_choi')}
                <span></span>
            </label> 
        </div>
    </div>

    <div class="col-12 col-lg-6">
	    <div config-email class="collapse {if !empty($wheel_fortune.config_email.receive)}show{/if}">
	        <div class="row">
	        	<!-- <div class="col-12 col-md-6">
	        		<div class="form-group">
	                    <label>
	                        {__d('admin', 'tieu_de_email')}
	                        <span class="kt-font-danger">*</span>
	                    </label>
	                    <input id="title_email" name="config_email[title]" value="{if !empty($wheel_fortune.config_email.title)}{$wheel_fortune.config_email.title}{/if}" class="form-control form-control-sm" type="text" placeholder="{__d('admin', 'tieu_de_email')}" autocomplete="off">
	                </div>
	        	</div> -->

	        	<div class="col-12 col-md-6">
	        		<div class="form-group">
	                    <label>
	                        {__d('admin', 'email_nhan_tin')}
	                        <span class="kt-font-danger">*</span>
	                    </label>
	                    <input id="email" name="config_email[email]" value="{if !empty($wheel_fortune.config_email.email)}{$wheel_fortune.config_email.email}{/if}" class="form-control form-control-sm" type="text" placeholder="{__d('admin', 'email_nhan_tin')}" autocomplete="off">
	                </div>
	        	</div>
	        </div>
	    </div>
    </div>
</div>

