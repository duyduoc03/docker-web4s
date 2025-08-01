"use strict";

var nhLadipage = function () {

	var formEl;
	var validator;

	var initEditor = function() {

		// init editor
		var editor1 = ace.edit('embed-code-content');
		var htmlMode = ace.require('ace/mode/html').Mode;
		editor1.setTheme('ace/theme/monokai');
		editor1.session.setMode(new htmlMode());
		editor1.setShowPrintMargin(false);

	}

	var initValidation = function() {

  		nhMain.validation.url.init();

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 255
				},
				link: {
					required: true,
					maxlength: 255,
					url: true
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                link: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                }
            },

            errorPlacement: function(error, element) {
            	var messageRequired = element.attr('message-required');
            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
            		error.text(messageRequired);
            	}
            	error.addClass('invalid-feedback')

                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error);
                }else if(element.hasClass('select2-hidden-accessible')){
            		element.closest('.form-group').append(error);
                }else{
                	element.after(error);
                }
            },

			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});
	}

	var initSubmit = function() {

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			if (validator.form()) {
				
				var _editor1 = ace.edit("embed-code-content");
				// get content tinymce editor
				$('#content').val(_editor1.getValue());

				nhMain.initSubmitForm(formEl, $(this));
			}
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();
			initEditor();
			
			$('.kt-select-multiple').select2();
			$('.kt-selectpicker').selectpicker();
		}
	};
}();

$(document).ready(function() {
	nhLadipage.init();
});
