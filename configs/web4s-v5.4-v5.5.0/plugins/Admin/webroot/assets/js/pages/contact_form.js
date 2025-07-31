"use strict";

var nhContactForm = function () {

	var formEl;
	var validator;
	var initValidation = function() {

  		nhMain.validation.url.init();

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 255
				}
			},
			messages: {
				name: {
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
			},
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
			
			$('#wrap-list-field').repeater({
	            initEmpty: false,	            
	            show: function () {

	            	var tagify = new Tagify($(this).find('.tagify-input')[0], {
	                    pattern: /^.{0,45}$/,
	                    delimiters: ", ",
	                    maxTags: 10
	                });

	                var inputType = $(this).find('select[data-name="input_type"]').val() || '';	            	
	                $(this).find('[nh-wrap="item-option"]').toggleClass('d-none', inputType == '' ? true : false);

	                $(this).find('input[type="radio"][value="0"]').trigger('click');

	                $(this).find('.kt-selectpicker').selectpicker();
	                $(this).slideDown();
	          	
	            },
	            hide: function (deleteElement) {
	                $(this).slideUp(deleteElement);
	            }   
	        });
			
			$(document).on('change', 'select[data-name="input_type"]', function(e) {	            
	            var value = $(this).val() || '';
	            var itemElement = $(this).closest('.wrap-item');
	          	
	            $('.kt-selectpicker').selectpicker();
	            if (itemElement.length === 0) return;
	            itemElement.find('[nh-wrap="item-option"]').toggleClass('d-none', value != 'single_select' && value != 'multiple_select');
	            
	        });

	        // get oath url login
			$(document).on('click', '[btn-oauth-google-sheet]', function(e){
				var btnElement = $(this);

				var spreadsheet_id = $('input[name="spreadsheet_id"]').val() || '';
				var pathname = window.location.pathname;

				nhMain.callAjax({
					url: adminPath + '/contact/get-link-oauth-google-sheet',
					data: {
						spreadsheet_id: spreadsheet_id,
						pathname: pathname
					}
				}).done(function(response) {
				   	var code = response.code || _ERROR;
		        	var message = response.message || '';
		        	var data = response.data || {};
		        	var url = data.url || '';
		        	
		            if (code == _SUCCESS) {
		            	window.location.href = url;
		            } else {
		            	toastr.error(message);
		            }
				});
			});

			// hủy cấu hình ủy quyền email
			$(document).on('click', '[btn-deauthorize-email]', function(e){
				var btnElement = $(this);

				var form_id = btnElement.data('id') || '';
				if(form_id == '') toastr.error(nhMain.getLabel('du_lieu_khong_hop_le'));

				swal.fire({
			        title: nhMain.getLabel('xoa_uy_quyen_email'),
			        text: nhMain.getLabel('ban_co_chac_muon_xoa_uy_quyen_email_nay_khong'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			            KTApp.blockPage(blockOptions);
			            
			    		nhMain.callAjax({
							url: adminPath + '/contact/deauthorize-email',
							data: {
								form_id: form_id
							}
						}).done(function(response) {
			                KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	toastr.info(message);
				            	location.reload();
				            } else {
				            	toastr.error(message);
				            }
						});
			    	}    	
			    });
				return false;
			});

			// cau hinh ma google sheet
			$(document).on('click', '[btn-config-spreadsheet]', function(e){
				var btnElement = $(this);

				var form_id = $(this).data('id') || '';
				var spreadsheet_id = $('input[name="spreadsheet_id"]').val() || '';

				if(form_id == ''){
					toastr.error(nhMain.getLabel('du_lieu_khong_hop_le'));
					return false;
				} 

				if(spreadsheet_id == ''){
					toastr.error(nhMain.getLabel('vui_long_nhap_ma_bang_tinh'));
					return false;
				} 

				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: adminPath + '/contact/config-spreadsheet',
					data: {
						spreadsheet_id: spreadsheet_id,
						form_id: form_id
					}
				}).done(function(response) {
					KTApp.unblockPage();
				   	var code = response.code || _ERROR;
		        	var message = response.message || '';
		        	var data = response.data || {};
		        	var url = data.url || '';
		        	
		            if (code == _SUCCESS) {
		            	toastr.info(message);
		            	window.location.href = url;
		            } else {
		            	toastr.error(message);
		            }
				});
			});

			// hủy cấu hình google sheet
			$(document).on('click', '[cancel-google-sheet]', function(e){
				var btnElement = $(this);

				var form_id = btnElement.data('id') || '';
				if(form_id == '') toastr.error(nhMain.getLabel('du_lieu_khong_hop_le'));

				swal.fire({
			        title: nhMain.getLabel('xoa_cau_hinh_bang_tinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_cau_hinh_bang_tinh'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			            KTApp.blockPage(blockOptions);
			            
			    		nhMain.callAjax({
							url: adminPath + '/contact/cancel-config-spreadsheet',
							data: {
								form_id: form_id
							}
						}).done(function(response) {
			                KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	toastr.info(message);
				            	location.reload();
				            } else {
				            	toastr.error(message);
				            }
						});
			    	}    	
			    });
				return false;
			});

			$('.tagify-input').each(function() {
	            var tagify = new Tagify(this, {
	                pattern: /^.{0,45}$/,
	                delimiters: ", ",
	                maxTags: 10
	            });
	        });
			$('.kt-selectpicker').selectpicker();

	        initValidation();
			initSubmit();
		}

	};
}();

$(document).ready(function() {
	nhContactForm.init();
});
