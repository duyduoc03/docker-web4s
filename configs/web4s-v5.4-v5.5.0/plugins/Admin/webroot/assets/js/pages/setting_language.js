"use strict";

var nhSettingLanguage = {
	init: function(){
		var self = this;

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});

		$(document).on('change', '[name="auto_translate"]', function(e) {
			var checkTranslate = $(this).val();
	    	$('[nh-wrap="translate"]').toggleClass('d-none', checkTranslate == 1 ? false : true);
		});
	}
}

$(document).ready(function() {
	nhSettingLanguage.init();
});
