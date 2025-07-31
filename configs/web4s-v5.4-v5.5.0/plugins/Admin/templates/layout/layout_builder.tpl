<!DOCTYPE html>
<html lang="{$lang}">

	<!-- begin::Head -->
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>
			{if !empty($title_for_layout)}
				{$title_for_layout}
			{else}
				Control Panel
			{/if} 
			| Admin
		</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link href="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.min.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/style.bundle.min.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/plugins/global/ace-diff/ace-diff.min.css" rel="stylesheet" type="text/css" />

		<link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />

		<link href="{ADMIN_PATH}/assets/css/custom.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/layout-builder/css/main.css?v={$smarty.now}" rel="stylesheet" type="text/css" />
		
	</head>
	<body>
		
		{$this->fetch('content')}

		<script type="text/javascript">
			var adminPath = '{ADMIN_PATH}';	
			var cdnUrl = '{CDN_URL}';
			var paginationLimitAdmin = '{PAGINATION_LIMIT_ADMIN}';
			var templatePath = '{URL_TEMPLATE}';
			var csrfToken = '{$this->getRequest()->getAttribute('csrfToken')}';
			var accessKeyUpload = "{$access_key_upload}";
			var useMultipleLanguage = Boolean("{$use_multiple_language}");
			var listLanguage = JSON.parse('{$list_languages|@json_encode}');
			var languageAdmin = "{LANGUAGE_ADMIN}";
		</script>

		<script src="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/scripts.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/ace.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/theme-monokai.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/mode-json.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/mode-html.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/mode-smarty.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace/ext-language_tools.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace-diff/ace_1.3.3.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/ace-diff/ace-diff.min.js" type="text/javascript"></script>

		<script src="{ADMIN_PATH}/assets/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>		
		<script src="{ADMIN_PATH}/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.vi.min.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/block_config.js?v={$smarty.now}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/view_logs_file.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>

	    <script src="{ADMIN_PATH}/layout-builder/js/layout.js?v={$smarty.now}"></script>

	</body>

	<!-- end::Body -->
</html>