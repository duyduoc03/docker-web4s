<div class="kt-login__signin">
	<div class="kt-login__head" style="margin: 0 0 20px 0;">
		<h3 class="kt-login__title">
			{__d('admin', 'dang_nhap_quan_tri')}
		</h3>
	</div>

	<form id="form-login" class="kt-form" action="{ADMIN_PATH}/ajax-login" method="post">
		<div class="input-group form-group">
			<input name="username" class="form-control" type="text" placeholder="{__d('admin', 'tai_khoan')}" autocomplete="off">
		</div>

		<div class="input-group form-group">
			<input name="password" class="form-control" type="password" placeholder="{__d('admin', 'mat_khau')}" >
		</div>									

		<div nh-show-error class="text-error"></div>

		<div class="row kt-login__extra d-flex justify-content-between">
			<label class="kt-checkbox kt-checkbox--tick kt-checkbox--success kt-font-bold">
				<input name="token" type="checkbox" value="{if !empty($token)}{$token}{/if}"> 
				I'm not a robot
				<span></span>
			</label>

			<a href="javascript://" show-form-forgot class="color-gray kt-font-bold">
				{__d('admin', 'quen_mat_khau')}?
			</a>
		</div>

		<input type="hidden" name="redirect" value="{if !empty($redirect)}{$redirect}{/if}">

		<div class="kt-login__actions" style="margin-top: 10px;">
			<span id="btn-login" class="btn btn-dark btn-pill kt-login__btn-primary">
				{__d('admin', 'dang_nhap')}
			</span>
		</div>

		<div class="kt-login__account">
			<i class="text-muted" style="font-size: 12px;">
				© {$smarty.now|date_format:'%Y'} Web4s. 
				Version {ADMIN_VERSION_UPDATE}
			</i>
		</div>
	</form>
</div>

<div class="kt-login__forgot">
	<div class="kt-login__head">
		<h3 class="kt-login__title">
			{__d('admin', 'quen_mat_khau')} ?
		</h3>
		<div class="kt-login__desc">
			{__d('admin', 'nhap_email_de_lay_ma_xac_nhan_quen_mat_khau')}
		</div>
	</div>
	<div class="kt-login__form">
		<form id="forgot-password" class="kt-form" action="{ADMIN_PATH}/ajax-forgot-password" method="post">
			<div class="form-group">
				<input type="text" name="email" class="form-control" placeholder="Email" autocomplete="off">
			</div>
			<div class="kt-login__actions">
				<button id="btn-forgot-password" class="btn btn-dark btn-pill kt-login__btn-primary">
					{__d('admin', 'lay_ma')}
				</button>
				<button id="btn-cancel" class="btn btn-secondary btn-pill kt-login__btn-secondary">
					{__d('admin', 'quay_lai')}
				</button>
			</div>
		</form>
	</div>
</div>