"use strict";

var attributeByCategory = {
	wrapElement: $('#attributes-article'),
	mainCategoryInput: $('#main_category_id'),
	init: function(){
		var self = this;

		if(self.wrapElement.length == 0 || self.mainCategoryInput.length == 0) return;

		var apply = self.mainCategoryInput.attr('nh-attribute-by-category');
		if(typeof(apply) == _UNDEFINED || apply == 0 || !nhMain.utilities.parseInt(apply) > 0) return;

		self.events();
	},
	events: function(){
		var self = this;

		self.mainCategoryInput.on('refreshed.bs.select changed.bs.select', function(e) {
			self.loadAttributeProduct(this.value);
		});	
	},
	loadAttributeProduct: function(category_id = null){
		var self = this;

		if(category_id == null || typeof(category_id) == _UNDEFINED || !category_id > 0) return;

		KTApp.blockPage(blockOptions);
		nhMain.callAjax({
    		async: false,
    		dataType: 'html',
			url: adminPath + '/article/load-attribute-by-category',
			data: {
				category_id: category_id
			}
		}).done(function(response) {

			self.wrapElement.html(response);
			nhMain.attributeInput.init();

        	KTApp.unblockPage();
        	
		});
	}
}

var nhArticle = {
	formElement: $('#main-form'),
	validator: '',
	init: function(){
		var self = this;

		if(self.formElement.length == 0) return;
		self.initLib();
		self.validation(); 
		self.events();
	},
	initLib: function(){
		var self = this;

		nhMain.validation.url.init();

		nhMain.attributeInput.init();
		attributeByCategory.init();

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		nhMain.selectMedia.single.init();
		nhMain.selectMedia.album.init();
		nhMain.selectMedia.video.init({
			input: $('#url_video')
		});

		nhMain.selectMedia.file.init();
		nhMain.tinyMce.simple();
		nhMain.tinyMce.full(
			{
	        	keyup:function (a) {
		            nhSeoAnalysis.getContentWhenKeyUpTinyMCE(a);
		        }
		    }, function(editor){
		    	$('.btn-save').removeClass('disabled');
		    }
	    );

		nhMain.input.touchSpin.init($('input[name="position"]'), {
			prefix: '<i class="la la-sort-amount-desc"></i>',
			max: 9999999999,
			step: 1
		});

		$('.kt-select-multiple').select2();
		$('.kt-selectpicker').selectpicker();

		nhMain.tagSuggest.init();
		
        nhSeoAnalysis.init();

        nhMain.mainCategory.init({
        	wrapCategory: ['#wrap-category']
		});
		
	},
	events: function(){
		var self = this;

		// copy embed attribute
		$(document).on('click', '[nh-embed-attribute]', function(e) {
			e.stopImmediatePropagation();
			e.preventDefault();

			var embed = $(this).attr('nh-embed-attribute');
			if(embed.length == 0) return;

			nhMain.nhEvents.copy(embed, function(){
				toastr.success(nhMain.getLabel('da_copy_ma_nhung'));
			});
		});

		// save
		$(document).on('click', '.btn-save:not(.disabled)', function(e) {
			e.stopImmediatePropagation();
			e.preventDefault();
			
			if (!self.validator.form()) return;

			var resultScore	= nhSeoAnalysis.getScore();
			$('#seo-score').val(resultScore.seoScore);
			$('#keyword-score').val(resultScore.seoKeywordScore);

			
			// get content tinymce editor
			$('#description').val(tinymce.get('description').getContent());
			$('#content').val(tinymce.get('content').getContent());
			
			nhMain.initSubmitForm(self.formElement, $(this));

		});

		$(document).on('click', '.btn-save-draft:not(.disabled)', function(e) {
			e.stopImmediatePropagation();
			e.preventDefault();
			
			self.formElement.find('input[name="draft"]').val(1);
			$('#btn-save').trigger('click');
		});

		$(document).on('click', '[nh-scroll-top]', function(e) {
			$('html, body').animate({
				scrollTop: $('html, body').offset().top,
			});
		});
	},
	validation: function(){
		var self = this;

		self.validator = self.formElement.validate({
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
}

$(document).ready(function() {
	nhArticle.init();
});
