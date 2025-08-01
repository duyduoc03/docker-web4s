"use strict";

var wheelFortune = {
	formElement: $('#main-form'),
	validator: null,
	init: function(){
		var self = this;

		if(self.formElement.length == 0) return;

		$('#config_behavior').selectpicker();
		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		$('.kt_datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true,
		});

		self.managerItem.init();
		self.validation();
		self.event();
	},
	event: function(){
		var self = this;

		$(document).on('change', '[nh-config]', function(e) {
			var nameConfig = $(this).attr('nh-config') || '';
			if(nameConfig == '') return;

			$('[config-'+nameConfig+']').collapse($(this).is(':checked') ? 'show' : 'hide');

			if($(this).is(':checked') && nameConfig == 'email') {
				$('#title_email').rules('add', {
					required: true,
                    maxlength: 255,
                    messages: {
                    	required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                    }
				});
				$('#email').rules('add', {
					required: true,
					email: true,
                    messages: {
                    	required: nhMain.getLabel('vui_long_nhap_dia_chi_email'),
                    	email: nhMain.getLabel('email_chua_dung_dinh_dang')
                    }
				});
			}
		});

		$(document).on('change', '#config_behavior', function(e) {
			var value = $(this).val();

			if(value > 0) {
				$('#occurance_after_delay').rules('add', {
					required: true,
                    messages: {
                    	required: nhMain.getLabel('vui_long_nhap_thong_tin')
                    }
				});
			}
		});

		$(document).on('change', '#check_limit', function(e) {
			var btnCheck = $(this);

			$('[data-name="type_award"]').each(function( index ) {
				var type_award = $(this).val() || 'nothing';
				if(type_award !== 'nothing' && btnCheck.is(':checked')) {
					$(this).closest('.wrap-item').find('[data-name="limit_prize"]').removeAttr('disabled');
				}else{
					$(this).closest('.wrap-item').find('[data-name="limit_prize"]').attr('disabled', 1)
				}
			});
		});

		$(document).on('change', '[data-name="type_award"]', function(e) {
			var type_award = $(this).val() || 'nothing';
			
			var wrapItem = $(this).closest('.wrap-item');
			var prizeValue = wrapItem.find('[prize-value]');

			var name = prizeValue.find('[name]').attr('name');
			var itemActive = wrapItem.find('[data-type="'+type_award+'"]');

			wrapItem.find('[data-type]').addClass('d-none').removeAttr('name');
			itemActive.removeClass('d-none').attr('name', name).removeAttr('disabled');
			if(type_award == 'nothing') {
				itemActive.attr('disabled', 'disabled');
				wrapItem.find('[data-name="limit_prize"]').attr('disabled', 'disabled');
			}

			if($('#check_limit').is(':checked') && type_award != 'nothing') {
				wrapItem.find('[data-name="limit_prize"]').removeAttr('disabled');
			}
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			var total_percent = 0;
			$('[data-name="percent_winning"]').each(function(){
				var percent = parseInt($(this).val());
				total_percent = total_percent + percent;
			});

			if(total_percent !== 100) {
				toastr.error(nhMain.getLabel('tong_co_hoi_phai_bang_100_vui_long_kiem_tra_lai_thong_tin'));
				return;
			}

			if (self.validator.form()) {
				nhMain.initSubmitForm(self.formElement, $(this));
			}
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
				winning_chance: {
					required: true,
					max: 100
				},
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                winning_chance: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    max: nhMain.getLabel('co_hoi_trung_thuong_nho_hon_hoac_bang_100')
                },
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
	},
	managerItem:{
		idWrap: '#wrap-wheel-option',
		classItem: '.wrap-item',
		itemHtml: null,
		formElement: null,
		init: function(options = {}){
			var self = this;

			self.itemHtml = $(self.idWrap + ' ' + self.classItem + ':first-child').length ? $(self.idWrap + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

			self.colorPicker();

			$(self.idWrap).find(self.classItem).each(function(index) {
			  	self.initInputItem($(this));
			});

			self.clearAllErrorItem();

			self.formElement = $(self.idWrap).closest('form')[0];

			$(self.formElement).on('click', '#add-item', function(e) {
				self.addNewItem();
			});

			$(document).on('click', self.idWrap + ' .btn-delete-item', function(e) {
				var item = $(this).closest(self.classItem);
				swal.fire({
			        title: nhMain.getLabel('xoa_ban_ghi'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_ban_ghi_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		item.remove();
			    	}
			    });
			});

			$(self.formElement).on('click', self.idWrap + ' .btn-toggle-item', function(e) {
				var item = $(this).closest(self.classItem);

				var hidden = item.hasClass('kt-portlet--collapse');

				if(hidden){
					item.find('.kt-portlet__body').slideDown();
					item.removeClass('kt-portlet--collapse');
				}else{
					item.find('.kt-portlet__body').slideUp();
					item.addClass('kt-portlet--collapse');
				}
			});

			$(document).on('keyup', self.idWrap + ' ' + self.classItem +  ' input.item-name', function(e) {
				var name = $(this).val();
				if(name.length == 0){
					name = nhMain.getLabel('giai_thuong');
				}

				var item = $(this).closest(self.classItem);
				item.find('.kt-portlet__head-title').text(name);
			});		

			$(document).on('click', '.btn-clear-image', function(e) {
		    	var wrap = $(this).closest('.kt-avatar');
		    	wrap.find('.kt-avatar__holder').css('background-image', '');
		    	wrap.removeClass('kt-avatar--changed');
		    	wrap.find('input[type="hidden"]').val('');
		    });  	
		},
		colorPicker: function(){
			var self = this;

			$(self.idWrap +' .demo').each( function() {
		        $(this).minicolors({
		          	control: $(this).attr('data-control') || 'hue',
		         	defaultValue: $(this).attr('data-defaultValue') || '',
		          	format: $(this).attr('data-format') || 'hex',
		          	keywords: $(this).attr('data-keywords') || '',
		          	inline: $(this).attr('data-inline') === 'true',
		          	letterCase: $(this).attr('data-letterCase') || 'lowercase',
		          	opacity: $(this).attr('data-opacity'),
		          	position: $(this).attr('data-position') || 'bottom',
		          	swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
		          	change: function(value, opacity) {
			            if( !value ) return;
			            if( opacity ) value += ', ' + opacity;
			            if( typeof console === 'object' ) {
		              		// console.log(value);
		            	}
		          	},
		          	theme: 'bootstrap'
		        });

	      	});
		},
		initInputItem: function(item = null){
			var self = this;
			if(item == null || item.length == 0) return;

			var indexItem = $(self.classItem).index(item[0]);

			// replace name input
			self.replaceNameInput(item);

			item.find('.kt-selectpicker').selectpicker('refresh');

			if($('#check_limit').is(':checked')) {
				item.find('[limit_prize]').removeClass('d-none');

				var type_award = item.find('select[data-name="type_award"]').val() || 'nothing';
				if(type_award == 'nothing') item.find('[limit_prize]').addClass('d-none');
			}else{
				item.find('[limit_prize]').addClass('d-none');
			}

			// sortable item menu
			$(self.idWrap).sortable({
                connectWith: '.kt-portlet__head',
                items: '.kt-portlet' + self.classItem,
                opacity: 0.8,
                handle : '.kt-portlet__head',
                coneHelperSize: true,
                placeholder: 'kt-portlet--sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                helper: 'clone',
                tolerance: 'pointer',
                forcePlaceholderSize: !0,
                cancel: '.kt-portlet--sortable-empty',
                revert: 250,
                update: function( event, ui ) {
                	$(self.idWrap).find(self.classItem).each(function(index) {
					  	self.replaceNameInput($(this));
					});
                }
            });
		},
		replaceNameInput: function(item){
			var self = this;
			var indexItem = item.index();
			$('input, select, textarea', item).each(function () {

				var dataName = $(this).attr('data-name');
				var dataContent = $(this).attr('data-content');
				if (typeof(dataName) == _UNDEFINED) return;

				var name = 'options['+ indexItem +'][' + dataName + ']';
				if(dataContent > 0) {
					name = 'options['+ indexItem +'][content][' + dataName + ']';
				}

				if(typeof($(this).attr('data-mutiple')) != _UNDEFINED){
					name += '[]';
				}

				if(dataName !== 'value') $(this).attr('name', name);
					
			});
		},
		addNewItem: function(){
			var self = this;

			$(self.idWrap).append(self.itemHtml);
			var item = $(self.idWrap).find(self.classItem + ':last-child');

			self.clearDataItem(item);
			self.initInputItem(item);
			self.colorPicker();
		},
		clearDataItem: function(item = null){
			if(item == null || item.length == 0) return;
			var self = this;

			item.find('.kt-portlet__head-title').text(nhMain.getLabel('giai_thuong'));

			$('input, select, textarea', item).each(function () {
				var typeInput = $(this).attr('type');
				if(typeInput == 'checkbox'){
					$(this).prop('checked', false);
				}else{
					$(this).val('');
					$(this).attr('value', '');
				}
			});
		},
		showErrorItem: function(item = null){
			var self = this;
			if(item == null || item == _UNDEFINED || item.length == 0) return;
			item.addClass('item-error');
			KTUtil.scrollTo(item[0], nhMain.validation.offsetScroll);
		},
		clearAllErrorItem: function(){
			var self = this;

		},
		// addDataSelectedAfterSuggest: function(item = {}, params = {}, inputSuggest = null){
		// 	var self = this;
			
		// 	if($.isEmptyObject(item)) return;
		// 	if(self.checkDataExistAfterSuggest(item.id)) return;
		// 	if(inputSuggest == null || inputSuggest.length == 0) return;
			
		// 	var nameInput = 'config[data_ids][]';
		// 	if(typeof(params.data_name) != _UNDEFINED){
		// 		nameInput = params.data_name;
		// 	}

		// 	var tagHtml = 
		// 	'<span class="tagify__tag">\
	 //            <x class="tagify__tag__removeBtn" role="button"></x>\
	 //            <div><span class="tagify__tag-text">' + item.name + '</span></div>\
	 //            <input name="'+ nameInput +'" value="' + item.id + '" type="hidden">\
	 //        </span>';

		// },
		// checkDataExistAfterSuggest: function(id = null, inputSuggest = null){
		// 	var self = this;			
		// 	if(inputSuggest == null || inputSuggest.length == 0) return false;

		// 	return false;
		// },
	},
};


$(document).ready(function() {
	wheelFortune.init();
});
