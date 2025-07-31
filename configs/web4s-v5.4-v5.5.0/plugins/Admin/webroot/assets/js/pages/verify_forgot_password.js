"use strict";

var nhVerifyForgotPassword = {
	formEl: $('#verify-forgot-password'),
	validator: null,
	init: function(){
		var self = this;

		if(self.formEl.length == 0 || $('[nh-otp="input"]').length == 0) return false;
    	self.otp.init({
			wrap: ['[nh-forgot-password]']
		});

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
					email: true,
					minlength: 10,
					maxlength: 255
				}
				,
				new_password: {
					required: true,
					minlength: 6
				},
				confirm_password: {
					equalTo: '#new_password'
				}
			},
			messages: {
                email: {
                	required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                email: nhMain.getLabel('email_chua_dung_dinh_dang')
                },
                new_password: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan')
	            },
	            confirm_password: {
	                equalTo: nhMain.getLabel('xac_nhan_mat_khau_chua_chinh_xac')
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
			}
		});
	},
	events: function () {
		var self = this;

		$('[nh-otp="input"]').on('focus', function(e) {
			e.preventDefault();
			$('span.bootstrap-maxlength').remove();
		});

		$(document).on('click', '#confirm-password', function(e) {
			e.preventDefault();

			var btnSave = $(this);	

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
	            		toastr.success(message);
	            		window.location.href = '/admin';
		            } else {
		            	$('[nh-show-error]').text(message);
		            }
				});
			}
		});

		self.formEl.on('click', '#resend-verify', function(e){
	  		e.preventDefault();

	  		self.formEl.find('[name="new_password"]').rules('remove');
	  		self.formEl.find('[name="re_password"]').rules('remove');

	  		if (!self.validator.form()) return false;

			var formData = {
				email: self.formEl.find('[name="email"]').val(),
				generate_token: 'ad_forgot_password'
			}

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
				url: adminPath + '/resend-verify-code',
				data: formData
			}).done(function(response) {

				var countDownElement = $('[nh-countdown]');
				if(countDownElement == null || countDownElement == _UNDEFINED || countDownElement.length == 0){
					return false;
				}

				self.countDown.init(60,
  					function(sec){
  						countDownElement.text(nhMain.getLabel('gui_lai_sau') + ' ('+sec+')');
  						$('#resend-verify').addClass('disable');
  					},
  					function(){
  						countDownElement.text('');
  						$('#resend-verify').removeClass('disable');
  					}
  				);

			   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

	        	KTApp.unblockPage();
	            if (code == _SUCCESS) {
	            	toastr.success(message);
	            } else {
	            	toastr.error(message);
	            }
			});
		});

		self.formEl.on('keydown', 'input', function(e){
	  		if(e.keyCode == 13){
	  			self.formEl.find('[btn-action="submit"]').trigger('click');
	  			return false;
	  		}			  		
		});
	},
	otp: {
		wrapElement: null,
		inputOtp: null,
		inputVerification: null,
		init: function(params = {}) {
			var self = this;

			self.wrapElement = typeof(params.wrap) != _UNDEFINED ? params.wrap : [];	

			if(self.wrapElement == null || self.wrapElement == _UNDEFINED || self.wrapElement.length == 0){
				return;
			}

			$.each(self.wrapElement, function(index, wrapElement) {
				var inputOtp = '[nh-otp="input"]';
				var inputVerification = '[nh-otp="verification"]';

				if($(wrapElement).length == 0 || $(wrapElement).find(inputOtp).length == 0 || $(wrapElement).find(inputVerification).length == 0){
					return;
				}

				self.inputOtp = inputOtp;
				self.inputVerification = inputVerification;

				self.event();
			});
		},
		event: function() {
			var self = this;

			$(document).on('keypress', self.inputOtp, function(e){
				if (e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39 && e.which != 9 && (e.which < 48 || e.which > 57)) return false;	
			});

			$(document).on('keyup', self.inputOtp, function(e){
				var value = $(this).val();

				if(value.length > 0 && e.which != 46 && e.which != 8 && e.which != 37 && e.which != 39 && e.which != 9) {
					if (!isNaN(parseInt(e.key))) {
						$(this).val(e.key);
				    }	
					$(this).next().focus();
				} else {
					$(this).val(value);
				}

			    var codeOtp = [];
			    $(self.inputOtp).each(function(i) {
					codeOtp[i] = $(self.inputOtp)[i].value;					
				});

			    $(self.inputVerification).val(codeOtp.join(''));
			});

			$(document).on('keydown', self.inputOtp, function(e){
				switch(e.keyCode) {
					case 8://backspace
			        	if($(this).val().length > 0){
			        		$(this).val('');
			        	} else {
			        		$(this).prev().focus();
			        	}

			         	break;
			        case 46://del
			        	if($(this).val().length > 0){
			        		$(this).val('');
			        	} else {
			        		$(this).next().focus();
			        	}
			        	
			        	break;

			        case 37://key left
			        	$(this).prev().focus();
			        	
			        	break;

			        case 39://key right
			        	$(this).next().focus();
			        	
			        	break;
				}
			});

			$(document).on('paste', self.inputOtp, function(e){
				var pastedData = e.originalEvent.clipboardData.getData('text');
				var inputLength = $(self.inputOtp).length;
				
    			for (var i = 0; i < pastedData.length; i++) {
    				if (isNaN(parseInt(pastedData[i]))) {
    					toastr.error(nhMain.getLabel('Please enter number'));
				        return false;
				    }	

    				if(i < inputLength){
    					$(self.inputOtp)[i].value = pastedData[i];
    				}
    				if(i == (inputLength - 1)){
    					$(self.inputOtp)[i - 1].focus();
    				}	
    			}
			});
		}
	},
	countDown: {
		timer: null,
		init: function(seconds = 60, callBackDuring = null, callBackEnd = null) {
			var self = this;

			if (typeof(callBackDuring) != 'function') {
		        callBackDuring = function () {};
		    }

		    if (typeof(callBackEnd) != 'function') {
		        callBackEnd = function () {};
		    }

		    seconds = typeof(seconds) != _UNDEFINED ? seconds : null;

		    clearTimeout(self.timer);
			(function decrementCounter(){
			    if (--seconds < 0) {
			    	callBackEnd();
			    	return false;
			    }
			    self.timer = setTimeout(function(){
			        callBackDuring(seconds);
			        decrementCounter();
			    }, 1000);
			})();
		}
	},
}


$(document).ready(function() {
	nhVerifyForgotPassword.init();
});