"use strict";

var nhExtendCollection = {
	wizardElement: null,
	formElement: $('#collection-form'),
	init: function(){
		var self = this;
	
		if(self.formElement.length == 0) return;

		self.initLib();
		self.events();
	},
	initLib: function(){
		var self = this;

		if($('#wizard-extent-collection').length > 0){
			var startStep = $('#wizard-extent-collection').attr('data-step') || 1
			self.wizardElement = new KTWizard('wizard-extent-collection', {
				startStep: startStep,
				clickableSteps: true
			});

			self.toggleActionButton(startStep);
		}
		

		self.fieldsExtend.init();
	},
	events: function(){
		var self = this;

		self.wizardElement.on('beforeNext', function(wizardObj) {
			var step = wizardObj.getStep();
			if(step == 1){
				var check = self.validateCollectionInfo();
				if(!check) return false;
			}

			if(step == 2){
				if(!self.validateFieldBeforeSubmit()) return false;
				self.saveCollection(self.step);
				return false;
			}
		});	

		self.wizardElement.on('beforePrev', function(wizardObj) {
			var step = wizardObj.getStep();
			
			if(step == 1) return false;

			if(step == 2){
				if(!self.validateFieldBeforeSubmit()) return false;		
			}

			if(step == 3){
				wizardObj.goPrev();
			}
		});	

		self.wizardElement.on('change', function(wizardObj) {
			var step = wizardObj.getStep();
			self.toggleActionButton(step);
		});

		$(document).on('click', '[nh-btn="save-structure"]', function(e) {
			self.saveFormConfig();
		});		
	},
	validateCollectionInfo: function(){
		var self = this;

		nhMain.validation.error.clear(self.formElement);

		var collectionName = $('input#collection-name').val() || '';
		var collectionCode = $('input#collection-code').val() || '';

		if(collectionName == '') {
			nhMain.validation.error.show($('input#collection-name'), nhMain.getLabel('vui_long_nhap_thong_tin'));
			return false;
		}

		if(collectionCode == '') {
			nhMain.validation.error.show($('input#collection-code'), nhMain.getLabel('vui_long_nhap_thong_tin'));
			return false;
		}

		return true;
	},
	validateField: function(wrapField = null){
		var self = this;

		if(wrapField == null || typeof(wrapField) == _UNDEFINED || wrapField.length == 0) return false;
		if(wrapField.attr('nh-item') != 'field') return false; 
		
		nhMain.validation.error.clear(wrapField);

		var inputName = wrapField.find('input[data-name="name"]');
		var inputCode = wrapField.find('input[data-name="code"]');
		var selectInputType = wrapField.find('select[data-name="input_type"]');

		if(inputName.length == 0 || inputCode.length == 0 || selectInputType.length == 0) return false;

		var name = inputName.val() || '';
		var code = inputCode.val() || '';
		var inputType = selectInputType.val() || '';

		if(name == '') {
			nhMain.validation.error.show(inputName, nhMain.getLabel('vui_long_nhap_thong_tin'));
			return false;
		}

		if(code == '') {
			nhMain.validation.error.show(inputCode, nhMain.getLabel('vui_long_nhap_thong_tin'));
			return false;
		}

		if(inputType == '') {
			nhMain.validation.error.show(selectInputType, nhMain.getLabel('vui_long_chon_thong_tin'));
			return false;
		}
		
		var regExp = new RegExp(/^[a-z0-9_]{1,}$/);
		if(!regExp.test(code)) {
			nhMain.validation.error.show(inputCode, nhMain.getLabel('ma_khong_hop_le'));
			return false;
		}
		
		var index = $('[nh-item="field"]').index(wrapField);

		var listName = [];
		$('[nh-item="field"]').each(function(i) {
			if(i == index) return;

			var name = $(this).find('input[data-name="name"]').val() || '';
			if(name == '') return;
			listName.push(name);
		});

		if(listName.includes(name)) {
			nhMain.validation.error.show(inputName, nhMain.getLabel('ten_truong_da_ton_tai'));
			return false;
		}

		var listCode = [];
		$('[nh-item="field"]').each(function(i) {
			if(i == index) return;

			var code = $(this).find('input[data-name="code"]').val() || '';
			if(code == '') return;
			listCode.push(code);
		});

		if(listCode.includes(code)) {
			nhMain.validation.error.show(inputCode, nhMain.getLabel('ma_da_ton_tai'));
			return false;
		}

		return true;
	},
	validateFieldBeforeSubmit: function(){
		var self = this;

		var result = true;
		self.formElement.find('[nh-item="field"]').each(function(e) {
			var check = self.validateField($(this));
			if(check == false) result = false;
		});

		return result;
	},
	saveCollection: function(){
		var self = this;

		KTApp.blockPage(blockOptions);

		nhMain.callAjax({
			url: self.formElement.attr('action'),
			data: self.formElement.serialize()
		}).done(function(response) {
			KTApp.unblockPage();

		   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
            if (code == _SUCCESS) {            	
            	// toastr.success(_SUCCESS, message);
            	if(typeof(data.id) != _UNDEFINED){
            		window.location.href = `${adminPath}/extend-collection/update/${data.id}?step=3`;
            	}else{
            		location.reload();
            	}
            } else {
            	toastr.error(_ERROR, message);
            }
		});
	},
	saveFormConfig: function(){
		var self = this;

		KTApp.blockPage(blockOptions);

		var config = nhThemeConfig.getConfig();
		$('#structure-form').find('input[name="config"]').val(JSON.stringify(config));

		nhMain.callAjax({
			url: $('#structure-form').attr('action'),
			data: $('#structure-form').serialize()
		}).done(function(response) {
			KTApp.unblockPage();

		   	var code = response.code || _ERROR;
        	var message = response.message || '';
            if (code == _SUCCESS) {
            	toastr.success(_SUCCESS, message);
            	location.reload();
            } else {
            	toastr.error(_ERROR, message);
            }
		});
	},
	toggleActionButton: function(step = 1){
		var self = this;

		var prevButton = $('#wizard-extent-collection').find('[data-ktwizard-type="action-prev"]');
		var nextButton = $('#wizard-extent-collection').find('[data-ktwizard-type="action-next"]');
		var saveStructureButton = $('#wizard-extent-collection').find('[nh-btn="save-structure"]');		

		if(prevButton.length == 0 || nextButton.length == 0 || saveStructureButton.length == 0) return;

		if(step == 1) {
			prevButton.addClass('d-none').removeClass('d-block');
			nextButton.addClass('d-block').removeClass('d-none');			
			saveStructureButton.addClass('d-none').removeClass('d-block');
		}

		if(step == 2) {
			prevButton.addClass('d-block').removeClass('d-none');
			nextButton.addClass('d-block').removeClass('d-none');
			saveStructureButton.addClass('d-none').removeClass('d-block');
		}

		if(step == 3) {
			prevButton.addClass('d-block').removeClass('d-none');
			nextButton.addClass('d-none').removeClass('d-block');
			saveStructureButton.addClass('d-block').removeClass('d-none');
		}

	},
	fieldsExtend: {
		wrapElement: $('[nh-wrap="fields"]'),
		listElement: null,
		classItem: '.wrap-item',
		itemHtml: null,
		init: function(){
			var self = this;

			self.itemHtml = self.wrapElement.find('.wrap-item:first-child').length ? self.wrapElement.find('.wrap-item:first-child')[0].outerHTML : '';
			if(self.itemHtml == null || typeof(self.itemHtml) == _UNDEFINED || self.itemHtml.length == 0) return false;

			self.wrapElement.find(self.classItem).each(function(index) {
			  	self.initInputItem($(this));
			});

			$(document).on('click', '#add-field-extend', function(e) {
				self.addNewItem();
				// $('input[checked]').each(function(e) {
				// 	$(this).trigger('click');
				// });
			});

			$(document).on('click', '[wrap-manager="fields"] [nh-btn="delete"]', function(e) {
				e.stopImmediatePropagation();

				var itemElement = $(this).closest(self.classItem);
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
			    		if(self.wrapElement.find('.wrap-item').length > 1){
			    			itemElement.remove();
			    		}else{
			    			self.clearDataItem(itemElement);
			    		}
			    	}
			    });
			});

			$(document).on('click', '[wrap-manager="fields"] [nh-btn="toggle"]', function(e) {
				e.stopImmediatePropagation();

				var itemElement = $(this).closest(self.classItem);
				var hidden = itemElement.hasClass('kt-portlet--collapse');

				if(hidden){
					itemElement.find('.kt-portlet__body').slideDown();
					itemElement.removeClass('kt-portlet--collapse');
				}else{
					itemElement.find('.kt-portlet__body').slideUp();
					itemElement.addClass('kt-portlet--collapse');
				}				
			});

			$(document).on('keyup', '[wrap-manager="fields"] input.name-field', function(e) {
				e.stopImmediatePropagation();

				var name = $(this).val();
				if(name.length == 0){
					name = 'New Item';
				}

				var itemElement = $(this).closest(self.classItem);
				itemElement.find('.kt-portlet__head-title').text(name);
			});

			$('[wrap-manager="fields"]').on('change', 'input[data-name]', function(e) {
				nhExtendCollection.validateField($(this).closest('[nh-item="field"]'));
			});

			$(document).on('change', 'select[data-name]', function(e) {
				var value = $(this).val() || '';
				var itemElement = $(this).closest(self.classItem);

				if($(this).attr('data-name') == 'input_type'){
					if(value == 'single_select' || value == 'multiple_select'){
						itemElement.find('[nh-wrap="item-option"]').removeClass('d-none');
					}else{
						itemElement.find('[nh-wrap="item-option"]').addClass('d-none');
					}

					if(value == 'text' || value == 'rich_text'){
						itemElement.find('[nh-wrap="item-multiple-language"]').removeClass('d-none');
					}else{
						itemElement.find('[nh-wrap="item-multiple-language"]').addClass('d-none');
					}

					if(value == 'rich_text'){
						itemElement.find('[nh-wrap="item-view"]').addClass('d-none');						
					}else{
						itemElement.find('[nh-wrap="item-view"]').removeClass('d-none');
					}
					
				}

				nhExtendCollection.validateField($(this).closest('[nh-item="field"]'));
			});
		},
		initInputItem: function(itemElement = null){
			var self = this;
			if(itemElement == null || itemElement.length == 0) return;

			var indexItem = $(self.classItem).index(itemElement[0]);

			// replace name input
			self.replaceNameInput(itemElement);			
			

			// init selectpicker
			itemElement.find('.kt-selectpicker').each(function(index) {
				$(this).selectpicker();
			});

			itemElement.find('.tagify-input').each(function() {
	            var tagify = new Tagify(this, {
	                pattern: /^.{0,45}$/,
	                delimiters: ", ",
	                maxTags: 20
	            });
	        });

			// sortable itemElement menu
			$(self.wrapElement).sortable({
				axis: 'y',
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
	            cancel: '.kt-portlet--sortable-empty',
	            revert: 250,
	            update: function( event, ui ) {
	            	$(self.wrapElement).find(self.classItem).each(function(index) {
					  	self.replaceNameInput($(this));
					});
	            }
	        });
		},
		replaceNameInput: function(item){
			var self = this;
			var indexItem = item.index();

			var code = self.wrapElement.attr('nh-wrap');
			$('input, select, textarea', item).each(function () {
				if (typeof($(this).attr('data-name')) == _UNDEFINED) return;
				var name = code + '['+ indexItem +'][' + $(this).attr('data-name') + ']';
				$(this).attr('name', name);
			});
		},
		addNewItem: function(){
			var self = this;

			self.wrapElement.append(self.itemHtml);
			var itemElement = self.wrapElement.find(self.classItem + ':last-child');

			self.clearDataItem(itemElement);
			self.initInputItem(itemElement);

			itemElement.find('input').removeClass('disabled');
			itemElement.find('select.kt-selectpicker').removeAttr('disabled').selectpicker('refresh');
		},
		clearDataItem: function(itemElement = null){
			if(itemElement == null || itemElement.length == 0) return;
			var self = this;

			itemElement.find('.kt-portlet__head-title').text(nhMain.getLabel('them_truong'),);

			$('input, select, textarea', itemElement).each(function () {
				var typeInput = $(this).attr('type');

				if(typeInput == 'checkbox' || typeInput == 'radio'){
					// $('.kt-radio--success > [data-name="required"]').attr('checked', true);
				}else{
					$(this).val('');
					$(this).attr('value', '');
				}

				if($(this).attr('readonly')) $(this).attr('readonly', false);
			});
		}
	}
}

var nhThemeConfig = {
	idWrap: '#wrap-structure-template',
	classRowItem: '.nh-row-item',
	classColumnItem: '.nh-column-item',
	rowTemplate: '<div class="row nh-row-item"></div>',
	columnTemplate: '<div class="nh-column-item"><div class="nh-content-column"></div></div>',
	init: function(){
		var self = this;
		self.events();
		self.initObject();
	},
	events: function(){
		var self =  this;

		$(document).on('click', '.nh-btn-add-row', function(e) {
			var wrapListItem = $(this).closest('.nh-template-portlet').find('.wrap-list-item');
			self.addRow(wrapListItem);
		});

		$(document).on('click', self.idWrap + ' ' + self.classRowItem + ':not(.disable)', function(e) {
			if(!$(this).hasClass('active')){
				self.activeRowItem($(this));
			}
			self.showPopoverRowItem($(this));
		});

		$(document).on('click', '.row-setting-column', function(e) {
			$('#row-setting-column-modal').modal('show');

			var _popover = $(this).closest('.popover.lg-popover');
			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
			self.hiddenPopoverRowItem(rowItem);
		});

		$('#row-setting-column-modal').on('shown.bs.modal', function (e) {
			var rowItem = $(self.classRowItem + '.active');
			var numberColumn = rowItem.find(self.classColumnItem).length;
			var valueColumn = [];
			rowItem.find(self.classColumnItem).each(function(index) {
    			var value = typeof($(this).attr('data-column-value') != _UNDEFINED) ? nhMain.utilities.parseInt($(this).attr('data-column-value')) : null;
			  	valueColumn.push(value);
    		});
			
		 	nhColumnConfig.init({
		 		column: numberColumn, 
		 		value: valueColumn
		 	});
		});

		$(document).on('click', '.row-setting-delete', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

				self.hiddenPopoverRowItem(rowItem);

				swal.fire({
		        title: nhMain.getLabel('xoa_dong'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_dong_nay'),
		        type: 'error',
		        
		        confirmButtonText: '<i class="la la la-trash-o"></i>' + nhMain.getLabel('xoa_dong'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-secondary'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		rowItem.remove();
		    		self.clearRowItem();
		    	}
		    });
		});

		$(document).on('click', '.row-setting-copy', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

				self.hiddenPopoverRowItem(rowItem);

			swal.fire({
		        title: nhMain.getLabel('nhan_ban_dong'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_nhan_ban_dong_nay'),
		        type: 'info',
		        
		        confirmButtonText: '<i class="fa fa-copy"></i>' + nhMain.getLabel('nhan_ban'),
		        confirmButtonClass: 'btn btn-sm btn-brand',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-secondary'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		var htmlRow = rowItem[0].outerHTML;
		    		rowItem.after(htmlRow);
		    	
		    		var newRowItem = rowItem.next();
		    		newRowItem.attr('data-code', '');
		    		self.activeRowItem(newRowItem);
		    		self.initObject();
		    	}
		    });
		});

		$(document).on('click', '.row-setting-down', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');    		
			var htmlRowItem = rowItem[0].outerHTML;
			var rowAfter = rowItem.next(self.classRowItem);

			self.hiddenPopoverRowItem(rowItem);
			if(rowAfter.length > 0){
				rowItem.remove();
				rowAfter.after(htmlRowItem);
				self.showPopoverRowItem(rowItem);
			}    	
		});

		$(document).on('click', '.row-setting-up', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
			var htmlRowItem = rowItem[0].outerHTML;
			var rowBefore = rowItem.prev(self.classRowItem);

			self.hiddenPopoverRowItem(rowItem);
			if(rowBefore.length > 0){
				rowItem.remove();
    			rowBefore.before(htmlRowItem);
    			self.showPopoverRowItem(rowItem);
			}
		});

	},
	getConfig: function(){
		var self = this;
		var result = {
			rows: []
		};

		$(self.idWrap).find('.wrap-list-item').each(function(i) {
			var rows = [];
			$(this).find(self.classRowItem + ':not(.disable)').each(function(i) {
				var _row = $(this);
				var dataColumn = [];
				
				_row.find(self.classColumnItem).each(function(i) {
					var _column = $(this);
					var value = nhMain.utilities.parseInt(_column.attr('data-column-value'));

					var fields = [];
					_column.find('.field-item').each(function(i) {
						var code = $(this).attr('data-code') || '';
						if(code.length > 0){
							fields.push(code);	
						}    						
					});

					dataColumn.push({
						column_value: value,
						field: fields
					});
				});

				rows.push({
					columns: dataColumn
				});
			});

			result['rows'] = rows;    			
		});

		return result;
	},
	initObject: function(){
		var self = this;
		$(self.idWrap + ' .nh-content-column').sortable({
			start: function( event, ui ) {
				// hidden all popover
				$('.popover').popover('hide');
			}
		});

		$('.nh-list-block li.field-item').draggable({
			helper: 'clone',
    		connectToSortable: '.nh-row-item:not(.disable) .nh-content-column',
    		stop: function( event, ui ) {
    			if(typeof(ui.helper) != _UNDEFINED){
    				ui.helper.draggable({
    					containment: 'parent',
		        		connectToSortable: '.nh-content-column',
					});
    			}
    		}
		});
	},
	activeRowItem: function(rowItem = null){
		if(typeof(rowItem) == _UNDEFINED){
			return;
		}

		var self = this;
		self.clearRowItem();
		rowItem.addClass('active');
	},
	clearRowItem: function(){
		var self = this;
		$(self.classRowItem).removeClass('active');

		$('.popover-setting-row').popover('hide');
	},
	showPopoverRowItem: function(rowItem = null){
		var self = this;
		
		if(typeof(rowItem) == _UNDEFINED || rowItem.length == 0) return;

		// check exist popover of row
		// if(typeof(rowItem.attr('aria-describedby'))  != _UNDEFINED) return;

		// hidden all popover
		$('.popover').popover('hide');

		// set content popover
		var contentPopover = $('#popover-setting-row').html();
		var divContentPopover = $('<div></div>').html($('#popover-setting-row').html());
		if(rowItem.is(':first-child')){
    		divContentPopover.find('.row-setting-up').remove();
    	}

    	if(rowItem.is(':last-child')){
    		divContentPopover.find('.row-setting-down').remove();
    	}

    	// init popover
		rowItem.popover({
			placement: 'top',
			html: true,
			sanitize: false,
			trigger: 'focus',
            content: divContentPopover.html(),
           	template: '\
	            <div class="popover lg-popover popover-setting-row" role="tooltip">\
	                <div class="arrow"></div>\
	                <div class="popover-body p-10"></div>\
	            </div>'
        });

		rowItem.popover('show');

		// event after show
        rowItem.on('shown.bs.popover', function (e) {
        	var idPopover = rowItem.attr('aria-describedby');
        	var _popover = $('#' + idPopover);

        	_popover.find('[data-toggle="kt-tooltip"]').tooltip({
        		trigger: 'hover'
        	});
		})		
	},
	hiddenPopoverRowItem: function(rowItem = null){
		if(typeof(rowItem) == _UNDEFINED){
			return;
		}

		var idPopover = rowItem.attr('aria-describedby');
		var _popover = $('#' + idPopover);
		_popover.find('[data-toggle="kt-tooltip"]').tooltip('hide');
		rowItem.popover('dispose');
	},
	addRow: function(wrapListItem = null){
		if(typeof(wrapListItem) == _UNDEFINED){
			return;
		}

		var self = this;
		wrapListItem.append(self.rowTemplate);

		var newRow = wrapListItem.find(self.classRowItem + ':last-child');
		self.setNumberColumn(newRow, {column: 1, value: [12]})

		self.initObject();
	},
	setNumberColumn: function(rowItem = null, config = {}){
		if(typeof(rowItem) == _UNDEFINED){
			return;
		}

		var self = this;
		var column = typeof(config.column != _UNDEFINED) ? parseInt(config.column) : null;
		var valueColumn = typeof(config.value != _UNDEFINED) ? config.value : [];

		if(!column > 0 || $.isEmptyObject(valueColumn)){
			return;
		}

		rowItem.html('');
		for (var i = 0; i < column; i++) {
			rowItem.append(self.columnTemplate);
			var newColumn = rowItem.find(self.classColumnItem + ':last-child');
			var value = typeof(valueColumn[i] != _UNDEFINED) ? valueColumn[i] : 1;

			newColumn.addClass('col-' + value);
			newColumn.attr('data-column-value', value);
		}

		self.initObject();
	},
}

var nhColumnConfig = {
	idModal: '#row-setting-column-modal',
	idWrap: '#wrap-setting-column',
	table: null,
	widthTable: 0,
	numberColumn: 1,
	minWidth: 0,
	tdTemplate: '<td><span class="column-value"></span></td>',
	init: function(config = {}){
		var self = this;

		var numberColumn = typeof(config.column != _UNDEFINED) ? parseInt(config.column) : null;
		var configValue = typeof(config.value != _UNDEFINED) ? config.value : [];

		if(numberColumn > 0){
			self.numberColumn = numberColumn;
			$(self.idWrap + ' #number-column-select').val(self.numberColumn);
		}else{
			self.numberColumn = parseInt($(self.idWrap + ' #number-column-select').val());
		}

		self.table = $(self.idWrap + ' .nh-table-config-column');
		self.widthTable = parseInt(self.table.width());
		
		self.minWidth = parseInt(self.widthTable / 12);
		self.setNumberColumn(configValue);

		$(document).on('change', self.idWrap + ' #number-column-select', function(e) {
			self.numberColumn = parseInt($(this).val());
			self.setNumberColumn();
		});

		$(document).on('click', '#save-row-setting-column', function(e) {
			var tr = self.table.find('tr:first-child');
			var numberColumn = tr.find('td').length;

			var valueColumn = [];
			tr.find('td').each(function(index) {
			  	var value = $(this).find('.column-value').text();
			  	if(parseInt(value) > 0){
			  		valueColumn.push(parseInt(value));
			  	}				  	
			});

			var rowItem = $('.nh-row-item.active');

			nhThemeConfig.setNumberColumn(rowItem, {
				column: numberColumn,
				value: valueColumn
			});

			$(self.idModal).modal('hide');
		});
	},
	setNumberColumn: function(configValue = []){
		var self = this;

		var tr = self.table.find('tr');
		tr.html('');

		for (var i = 0; i < self.numberColumn; i++){
			tr.append(self.tdTemplate);
			var td = tr.find('td:last-child');
			var width = nhMain.utilities.parseInt(self.widthTable / self.numberColumn);

			if(!$.isEmptyObject(configValue) && typeof(configValue[i]) != _UNDEFINED){
				width = Math.round(nhMain.utilities.parseInt(self.widthTable / (12/configValue[i])));
			}

			td.css('width', width);	
			td.css('min-width', self.minWidth);
		}

		self.setValueColumn();
		self.resizableColumn(self.table);
	},
	resizableColumn: function(table = null){
		var self = this;
		var tr = typeof(table.find('tr')) != _UNDEFINED ? table.find('tr') : null;
		if (typeof(tr) == _UNDEFINED || tr == null) return;

		tr.find('td:not(:last-child)').each(function(index) {
		  	var div = $('<div class="nh-resize-column">');
		  	self.setListeners(div[0]);			  	
		  	$(this).append(div);			  	
		});
	},
	setListeners: function(div){
		var self = this;
		var pageX, curCol, nxtCol, curColWidth, nxtColWidth;

		div.addEventListener('mousedown', function (e) {
			curCol = e.target.parentElement;
			nxtCol = curCol.nextElementSibling;
			pageX = e.pageX; 

			var padding = 0;
			curColWidth = curCol.offsetWidth - padding;
			if (typeof(nxtCol) != _UNDEFINED && nxtCol != _UNDEFINED){
				nxtColWidth = nxtCol.offsetWidth - padding;
			}				
		});

		document.addEventListener('mousemove', function (e) {
			if (typeof(curCol) != _UNDEFINED && curCol != _UNDEFINED) {
				var diffX = e.pageX - pageX;

				if (typeof(nxtCol) != _UNDEFINED && nxtCol != _UNDEFINED){
					nxtCol.style.width = (nxtColWidth - (diffX)) + 'px';
				}				 
				curCol.style.width = (curColWidth + diffX) + 'px';

				self.setValueColumn();
			}
		});

		document.addEventListener('mouseup', function (e) {
			curCol = _UNDEFINED;
			nxtCol = _UNDEFINED;
			pageX = _UNDEFINED;
			nxtColWidth = _UNDEFINED;
			curColWidth = _UNDEFINED
		});
	},
	setValueColumn: function(){
		var self = this;    	
		var totalValue = 12;

		self.table.find('td').each(function(index) {
				var _width = $(this).width();
				var value = Math.round(_width / nhMain.utilities.parseInt(self.widthTable / 12));
				totalValue -= value;
				if (index === (self.numberColumn - 1) && totalValue != 0) {
	       		value += totalValue;
	        }

	        $(this).find('.column-value').text(value);
		});
	},
	resetModal: function(){
		var self = this;
	}
}

var nhBlockConfig = {
	idWrap: '#wrap-structure-template',
	// idModal: '#field-info-modal',
	init: function(){
		var self = this;

		$(document).on('click', function (e) {
		    if ($(e.target).closest('.field-item').length === 0 && $(e.target).closest('.popover-setting-block').length === 0) {
		        $('.popover-setting-block').popover('hide');
		    }
		});

		$(document).on('click', self.idWrap + ' .field-item', function(e) {
			e.stopPropagation();
			self.showPopoverBlock($(this));
		});

		$(document).on('click', '.block-setting-general', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var blockItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
			var codeBlock = nhMain.utilities.notEmpty(blockItem.attr('data-code')) ? blockItem.attr('data-code') : null;

			blockItem.popover('hide');
			if(codeBlock != null){
				window.open(adminPath + '/template/block/update/' + codeBlock, '_blank');
			}    			
		});

		$(document).on('click', '.block-setting-delete', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var blockItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

			blockItem.popover('hide');

			swal.fire({
		        title: nhMain.getLabel('xoa_block'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_block_nay_khoi_cau_hinh_trang'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		blockItem.remove();	
		    	}
		    });    			
		});
	},
	showPopoverBlock: function(blockItem = null){
		var self = this;
		if(typeof(blockItem) == _UNDEFINED || !blockItem.length > 0) return;

		// check exist popover of row
		if(typeof(blockItem.attr('aria-describedby'))  != _UNDEFINED) return;

		// hidden other popover
		$('.popover').popover('hide');

		// init popover
		blockItem.popover({
			placement: 'top',
			html: true,
			sanitize: false,
			trigger: 'focus',
            content: '\
            	<div class="row">\
			        <div class="col-12">\
			            <div class="btn-toolbar" role="toolbar">\
			                <span class="btn-group" role="group">\
			                    <span class="btn btn-sm btn-secondary block-setting-delete" data-toggle="kt-tooltip" data-placement="top" title="'+ nhMain.getLabel('xoa') +'">\
			                        <i class="fa fa-trash-alt m-0"></i>\
			                    </span>\
			                </span>\
			            </div>\
			        </div>\
			    </div>',
           	template: '\
	            <div class="popover lg-popover popover-setting-block" role="tooltip">\
	                <div class="arrow"></div>\
	                <div class="popover-body p-10"></div>\
	            </div>'
        });

        blockItem.popover('show');

        // event after show
        blockItem.on('shown.bs.popover', function (e) {
        	var idPopover = blockItem.attr('aria-describedby');
        	var _popover = $('#' + idPopover);

        	_popover.find('[data-toggle="kt-tooltip"]').tooltip({
        		trigger: 'hover'
        	});
		})		
	}
}

$(document).ready(function() {
	nhExtendCollection.init();

	nhThemeConfig.init();
	nhColumnConfig.init();
	nhBlockConfig.init();
});