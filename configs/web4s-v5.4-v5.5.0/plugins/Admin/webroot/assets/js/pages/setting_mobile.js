"use strict";

$(document).on('click', '.btn-save', function(e) {
	e.preventDefault();

	var formEl = $(this).closest('form');
	nhMain.initSubmitForm(formEl, $(this));
});

var nhSetting_mobile = {
	form: $('#main-form'),
	wrapElement: '#list-level',
	classItem: '.wrap-item',
	itemHtml: null,
	init: function(params = {}){
		var self = this;

		if(self.form.length == 0) return false;
		if(self.wrapElement.length == 0) return false;

		self.itemHtml = $(self.wrapElement + ' ' + self.classItem + ':first-child').length ? $(self.wrapElement + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});		

		self.event();
	},
	event: function(){
		var self = this;

		$(self.wrapElement).find(self.classItem).each(function(index) {
		  	self.initInputItem($(this));
		});

		$(document).on('click', '#add-new-level', function(e) {
			self.addNewItem();
		});

		$(document).on('click', '.btn-delete-item-new', function(e) {
			var itemElement = $(this).closest('.wrap-item');
		
			itemElement.remove();
		});

		$(document).on('click', '#tab-link-file', function(e) {
			var wrapItem = $(this).closest('.wrap-item');
			var inputLinkFile = wrapItem.find('#linkFile');

			if (wrapItem.length == 0 || inputLinkFile.length == 0) return false;
				
			var linkFile = inputLinkFile.val();
			
			if(linkFile != null && linkFile.length > 0){
				window.open(  linkFile, "_blank");
			}
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			$('textarea.mce-editor-simple').each(function (index) {
				$('#description_' + index).val(tinymce.get('description_'+index).getContent());
			});

			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});
	},
	addNewItem: function(){
		var self = this;

		$(self.wrapElement).append(self.itemHtml);
		var item = $(self.wrapElement).find(self.classItem + ':last-child');
		
		self.clearDataItem(item);
		self.initInputItem(item);
	},
	replaceNameInput: function(item){
		var self = this;
		var indexItem = $(self.classItem).index(item[0]);

		$('input, textarea.mce-editor-simple', item).each(function () {
			if (typeof($(this).attr('data-name')) == _UNDEFINED) return;
			var name = 'commissions[' + indexItem + ']['+ $(this).attr('data-name') +']';
			$(this).attr('name', name);
		});

		$('textarea.mce-editor-simple', item).each(function () {
			$(this).attr('id', 'description_'+ indexItem);
		});

		$('#key_level', item).val(indexItem);

		// replace data-url and id of input select image
		item.find('[btn-select-media-block]').each(function( index ) {
			var dataSrc = $(this).data('src');
			$(this).data('src', dataSrc + '_' + indexItem);
		});

		var inputImage = item.find('.input-select-image');			
		var _id = inputImage.attr('id');
		
		inputImage.attr('id', _id + '_' + indexItem);
		item.find('[block-preview-image]').attr('block-preview-image', _id + '_' + indexItem);
		item.find('[block-image-source]').attr('block-image-source', _id + '_' + indexItem);
	},
	initInputItem: function(item = null){
		var self = this;
		if(item == null || item.length == 0) return;

		// replace name input
		self.replaceNameInput(item);
		

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

	},
	clearDataItem: function(item = null){
		if(item == null || item.length == 0) return;
		var self = this;
		item.find('.kt-avatar__holder').css('background-image', '');

		$('input', item).each(function () {
			$(this).val('');
		});

		$('textarea.mce-editor-simple', item).each(function () {
			$(this).val('');
			$(this).html('');
		});
	}
};

$(document).ready(function() {
	nhSetting_mobile.init();
});
