"use strict";

var nhDataExtend = function () {

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

		nhMain.attributeInput.init();

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		// copy embed attribute
		$(document).on('click', '[nh-embed-attribute]', function(e) {
			var embed = $(this).attr('nh-embed-attribute');
			if(embed.length == 0) return;

			nhMain.nhEvents.copy(embed, function(){
				toastr.success(nhMain.getLabel('da_copy_ma_nhung'));
			});
		});

		// save
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			if (validator.form()) {

				// get content tinymce editor
				$('#description').val(tinymce.get('description').getContent());
				$('#content').val(tinymce.get('content').getContent());

				nhMain.initSubmitForm(formEl, $(this));
			}
		});

		$(document).on('click', '.btn-save-draft', function() {
			formEl.find('input[name="draft"]').val(1);
			$('#btn-save').trigger('click');
		});	
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();

			nhMain.selectMedia.single.init();
			nhMain.selectMedia.album.init();
			nhMain.selectMedia.video.init({
				input: $('#url_video')
			});
			nhMain.selectMedia.file.init();

			$('.kt-select-multiple').select2();
			$('.kt-selectpicker').selectpicker();

			nhMain.tinyMce.simple();
			nhMain.tinyMce.full({
	            keyup:function (a) {
	            	
	            }
	        });
            
            setTimeout(function(){
            	nhMain.scrollToAnchor.init();
            }, 1000);
		}
	};
}();


$(document).ready(function() {
	nhDataExtend.init();
});
