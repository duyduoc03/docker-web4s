"use strict";

var nhExtendData = {	
	formElement: $('#data-extend-form'),
	init: function(){
		var self = this;
	
		if(self.formElement.length == 0) return;

		self.initLib();
		self.events();
	},
	initLib: function(){
		var self = this;
		
		nhMain.attributeInput.init();
	},
	events: function(){
		var self = this;
		
		$(document).on('click', '[nh-btn="save"]', function(e) {
			var btnElement = $(this);

			$('[input-attribute="rich-text"]').each(function() {
				var inputId = $(this).attr('id') || '';
				if(inputId == '') return;

				$(`#${inputId}`).val(tinymce.get(`${inputId}`).getContent());
			});

			KTApp.progress(btnElement);
			KTApp.blockPage(blockOptions);

			var formData = self.formElement.serialize();

			nhMain.callAjax({
				url: self.formElement.attr('action'),
				data: formData
			}).done(function(response) {
				KTApp.unprogress(btnElement);
				KTApp.unblockPage();
				toastr.clear();

			   	var code = response.code || _ERROR;
	        	var message = response.message || '';
	        	var data = response.data || {};
	        	
	            if (code == _SUCCESS) {
	            	toastr.info(message);
	            	location.reload();
	            } else {
	            	toastr.error(message);
	            }
			});
		});
	}
}

$(document).ready(function() {
	nhExtendData.init();
});