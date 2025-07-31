"use strict";

var nhAuthor = function () {

	var formEl;
	var validator;

	var initValidation = function() {

  		nhMain.validation.url.init();

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				full_name: {
					required: true,
					maxlength: 255
				},			
				email: {
					pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    minlength: 10,
                    maxlength: 255
				}
			},
			messages: {
				full_name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                email: {
                	pattern: nhMain.getLabel('email_chua_dung_dinh_dang'),
                	minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
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
				var resultScore	= nhSeoAnalysis.getScore();
				$('#seo-score').val(resultScore.seoScore);
				$('#keyword-score').val(resultScore.seoKeywordScore);

				// get content tinymce editor
				$('#description').val(tinymce.get('description').getContent());
				$('#content').val(tinymce.get('content').getContent());
				
				nhMain.initSubmitForm(formEl, $(this));
			}
		});
	}

	var socialOthers = {
		wrapElement: $('.wrap-social-others'),
		classItem: '.wrap-item',
		itemHtml: null,
		init: function(){

			var self = this;

			if(self.wrapElement.length == 0) return;

			self.itemHtml = self.wrapElement.find(self.classItem + ':first-child').length ? self.wrapElement.find(self.classItem + ':first-child')[0].outerHTML : '';
			
			self.events();
		},
		events: function(){
			var self = this;

			$(document).on('click', '#add-new-social', function(e) {
				self.addNewItem();
			});
			$(document).on('click', '[nh-btn="delete-item-social"]', function(e) {
				e.stopImmediatePropagation();
				
				var itemSocial = self.wrapElement.find(self.classItem);
				var item = $(this).closest(itemSocial);
	
				swal.fire({
			        title: nhMain.getLabel('xoa_dong'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_dong_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('Đồng ý'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		if (itemSocial.length > 1) {
			    			item.remove();
			    		}else{
			    			self.clearRowItem(item);
			    		}	    					        
			    	}
			    });
			});	
		},

		addNewItem: function(){
			var self = this;

			self.wrapElement.append(self.itemHtml);
			var item = self.wrapElement.find(self.classItem + ':last-child');
			
			setTimeout(function() {
		        self.clearDataItem(item);
		    }, 0);
		},
		clearDataItem: function(item = null){
			if(item == null || item.length == 0) return;
			var self = this;
			item.find('.kt-portlet__head-label .kt-portlet__head-title').text('');
			item.find('input').val('');
		}
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();

			socialOthers.init();

			nhMain.selectMedia.single.init();
			nhMain.selectMedia.album.init();
			nhMain.selectMedia.video.init({
				input: $('#url_video')
			});
			nhMain.selectMedia.file.init();
			$('.kt-selectpicker').selectpicker();

			nhMain.input.touchSpin.init($('input[name="position"]'), {
				prefix: '<i class="la la-sort-amount-desc"></i>',
				max: 9999999999,
				step: 1
			});

			$('.kt-select-multiple').select2();
			$('.kt-selectpicker').selectpicker();

			nhMain.tagSuggest.init();
			nhMain.tinyMce.simple();
			nhMain.tinyMce.full({
	            keyup:function (a) {
	                nhSeoAnalysis.getContentWhenKeyUpTinyMCE(a);
	            }
	        });
            nhSeoAnalysis.init();
            
		}
	};
}();


$(document).ready(function() {
	nhAuthor.init();
});
