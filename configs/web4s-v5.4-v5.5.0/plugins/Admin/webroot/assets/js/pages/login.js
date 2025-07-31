"use strict";

var nhLogin = function () {

	var formEl;
	var validator;

	var initValidation = function() {
		validator = formEl.validate({
			ignore: ':hidden',
			rules: {
				username: {
					required: true
				},
				password: {
					required: true
				},
				token: {
					required: true
				}
			},
			messages: {
				username: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin')
	            },
	            password: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin')
	            },
	            token: {
	                required: nhMain.getLabel('vui_long_xac_nhan_thong_tin')
	            }
	        },
            errorPlacement: function(error, element) {                
                if (element.closest('.input-group').length > 0) {
                    element.closest('.input-group').append(error.addClass('invalid-feedback'));
                }else if(element.closest('.kt-checkbox').length > 0){
                	element.closest('.kt-checkbox').append(error.addClass('invalid-feedback'));
                }else{
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			},

		});
	}

	var initSubmit = function() {

		$(document).on('click', function(e) {
			if($(this).val() != '') $(this).siblings('.invalid-feedback').remove();
		});

		$(document).on('click', '#btn-login:not([disabled])', function(e) {
			e.preventDefault();

			var btnSave = $(this);

			var username = formEl.find('input[name="username"]');
			var password = formEl.find('input[name="password"]');

			if(username.val() == '') {
				nhMain.validation.error.show(username, nhMain.getLabel('vui_long_nhap_thong_tin'));
				return false;
			}

			if(password.val() == '') {
				nhMain.validation.error.show(password, nhMain.getLabel('vui_long_nhap_thong_tin'));
				return false;
			}

			if (validator.form()) {
				toastr.clear();

				KTApp.blockPage(blockOptions);

				var formData = formEl.serialize();			
				nhMain.callAjax({
					url: formEl.attr('action'),
					data: formData
				}).done(function(response) {

					KTApp.unblockPage();
				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	var urlRedirect = typeof(data.url_redirect) != _UNDEFINED ? data.url_redirect : null;		        	

		        	if(code == 301) {
		        		$('#message-login-modal [message-errors]').empty().append(message);
		        		$('#message-login-modal').modal('show');
		        		return false;
		        	}

		            if (code == _SUCCESS) {
		            	toastr.success(message);		            	           	
		            	if(urlRedirect.length > 0){
		            		window.location.href = urlRedirect;
		            	}else{
		            		location.reload();
		            	}
		            } else {
	            		$('[nh-show-error]').text(message);
		            }
				});
			}
		});

		formEl.on('keydown', 'input', function(e){
	  		if(e.keyCode == 13){
	  			formEl.find('#btn-login').trigger('click');
	  			return false;
	  		}			  		
		});
	}

	var events = function() {

		var login = $('[wrap-account]');

		$('[show-form-forgot]').click(function(e) {
            e.preventDefault();

            login.removeClass('kt-login--signin');
	        login.addClass('kt-login--forgot');
	        KTUtil.animateClass(login.find('.kt-login__forgot')[0], 'flipInX animated');
        });

        $('#btn-cancel').click(function(e) {
            e.preventDefault();
            
            login.removeClass('kt-login--forgot');
	        login.addClass('kt-login--signin');
	        KTUtil.animateClass(login.find('.kt-login__signin')[0], 'flipInX animated');
        });
	}

	return {
		init: function() {
			formEl = $('#form-login');	
			formEl.find('#btn-login').prop('disabled', false);

			initValidation();
			events();
			initSubmit();
		}
	};
}();

var nhForgotPassword = {
	formEl: $('#forgot-password'),
	validator: null,
	init: function(){
		var self = this;

		if(self.formEl.length == 0) return false;
    	
	 	self.initValidation();	 	
	 	self.events();	 	
	},
	initValidation: function(){
		var self = this;

		self.validator = self.formEl.validate({
			ignore: ':hidden',
			rules: {
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				email: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                email: nhMain.getLabel('email_chua_dung_dinh_dang')
	            }
	        },
            errorPlacement: function(error, element) {                
                if (element.closest('.input-group').length > 0) {
                    element.closest('.input-group').append(error.addClass('invalid-feedback'));
                }else if(element.closest('.kt-checkbox').length > 0){
                	element.closest('.kt-checkbox').append(error.addClass('invalid-feedback'));
                }else{
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, 150);
			},

		});
	},
	events: function () {
		var self = this;

		$(document).on('click', '#btn-forgot-password', function(e) {
			e.preventDefault();

			if (self.validator.form()) {
				toastr.clear();

				KTApp.blockPage(blockOptions);

				var formData = self.formEl.serialize();

				nhMain.callAjax({
					url: self.formEl.attr('action'),
					data: formData
				}).done(function(response) {
				   	var code = response.code || _ERROR;
		        	var message = response.message || '';
		        	var data = response.data || {};       
		        	KTApp.unblockPage();
		        	
		            if (code == _SUCCESS) {
		            	if(data.email) {
		            		toastr.success(message);
		            		window.location.href = adminPath + '/verify-forgot-password?email='+data.email;
		            	}
		            } else {
		            	toastr.error(message);
		            }
				});
			}
		});

		self.formEl.on('keydown', 'input', function(e){
	  		if(e.keyCode == 13){
	  			self.formEl.find('[btn-action="submit"]').trigger('click');
	  			return false;
	  		}			  		
		});
	}
}

$(document).ready(function() {
	nhLogin.init();
	nhForgotPassword.init();
});