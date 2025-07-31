<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>Web4s | {__d('admin', 'tai_khoan')}</title>

		<meta name="description" content="{__d('admin', 'dang_nhap')}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">

		<link href="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/pages/login/login-4.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/login.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />
		
	</head>
	
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
		<div class="kt-grid kt-grid--ver kt-grid--root">
			<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v4 kt-login--signin" id="kt_login" wrap-account>
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" style="background-color: #1b1b28;">
					<div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
						<div class="kt-login__container">
							<div class="kt-login__logo" style="margin-bottom: 30px;">
								<img alt="Web4s" src="{ADMIN_PATH}/assets/media/logos/logo4s-01.svg" style="width: 250px;">
							</div>
							
							{$this->fetch('content')}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="message-login-modal" class="modal fade" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title">
		                    {__d('admin', 'thong_bao')}
		                </h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                </button>
		            </div>
		            <div class="modal-body">
		                <div class="form-group" message-errors="true" style="font-size: 16px;">
                            
                        </div>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
		                    {__d('admin', 'dong')}
		                </button>
		            </div>
		        </div>
		    </div>
		</div>

		<script type="text/javascript">
			var adminPath = "{ADMIN_PATH}";
			var csrfToken = "{$this->getRequest()->getAttribute('csrfToken')}";
		</script>

		<script src="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/scripts.bundle.js" type="text/javascript"></script>

		<script src="{ADMIN_PATH}/assets/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		
		{if !empty($js_page)}
	        {foreach from = $js_page item = js_file}
	            <script src="{$js_file}" type="text/javascript"></script>
	        {/foreach}
	    {/if}
	
	</body>

</html>