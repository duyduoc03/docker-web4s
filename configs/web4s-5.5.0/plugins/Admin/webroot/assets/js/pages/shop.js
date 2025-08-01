"use strict";

var nhShop = function () {

	var formEl;
	var validator;

	var initValidation = function() {

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 500
				},
				email: {
					pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    minlength: 10,
                    maxlength: 255
				},
				address: {
					required: true,
					maxlength: 500
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                email: {
                	pattern: nhMain.getLabel('email_chua_dung_dinh_dang'),
                	minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                address: {
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
				nhMain.initSubmitForm(formEl, $(this));
			}
		});	
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();

			nhMain.location.init({
				idWrap: ['#main-form']
			});

			nhMain.input.touchSpin.init($('input[name="position"]'), {
				prefix: '<i class="la la-sort-amount-desc"></i>',
				max: 9999999999,
				step: 1
			});

			$('.kt-select-multiple').select2();
			$('.kt-selectpicker').selectpicker();
		}
	};
}();


$(document).ready(function() {
	nhShop.init();
});
