{assign var = email value = $this->Utilities->getParamsByKey('email')}

<div class="kt-login__signin">
	<div class="kt-login__head mb-2">
		<h3 class="kt-login__title">
			{__d('admin', 'Thiết lập mật khẩu mới')}
		</h3>
		<div class="text-center text-white">
			Nhập mã OTP để thay đổi mật khẩu
		</div>
	</div>

	<form id="verify-forgot-password" action="{ADMIN_PATH}/ajax-verify-forgot-password" method="post" autocomplete="off" class="kt-form">
	    <div class="form-group d-none">
	        {if !empty($email)}
	        	<input name="email" type="hidden" class="form-control" value="{$email}">
	        {/if}
	    </div>
	    
	    <div class="form-group position-relative session-otp mb-0" nh-forgot-password>
	    	<label for="email">
	            {__d('template', 'ma_xac_nhan')}: 
	            <span class="required">*</span>
	        </label>
            <div class="input-opt d-flex align-items-center justify-content-between">
                <input nh-otp="input" type="text" maxlength="1" />
                <input nh-otp="input" type="text" maxlength="1" />
                <input nh-otp="input" type="text" maxlength="1" />
                <input nh-otp="input" type="text" maxlength="1" />
                <input nh-otp="input" type="text" maxlength="1" autocomplete="off"/>
            </div>
            <input nh-otp="verification" name="code" type="hidden"/>
        </div>

        <div class="input-group">
            <input id="new_password" name="new_password" type="password" placeholder="{__d('template', 'mat_khau_moi')}" class="form-control">
        </div>

        <div class="input-group">
            <input id="confirm_password" name="confirm_password" type="password" placeholder="{__d('template', 'nhap_lai_mat_khau_moi')}" class="form-control">
        </div>

        <div nh-show-error class="text-error"></div>
	    
	    <div class="form-group kt-login__actions">
	        <span id="confirm-password" class="btn btn-submit btn-dark btn-pill kt-login__btn-primary">
	            {__d('template', 'doi_mat_khau')}
	        </span>
	    </div>
	    <div class="text-center">
		    <div id="resend-verify" class="btn btn-submit-1 kt-font-bold">
		        {__d('template', 'gui_lai_ma_xac_nhan')}?
		        <span class="ml-1" nh-countdown></span>
		    </div>
	    </div>
	</form>
</div>