"use strict";

var nhTranslateLocale = {
	formElement: $('#main-form'),

	init: function(){
		var self = this;
		if(self.formElement.length == 0) return;

		$('.kt-select-multiple').select2();
		$('.kt-selectpicker').selectpicker();

		self.events();

	},
	events: function(){
		var self = this;

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
						      
	        self.translate(); 
	        		
		});
	},
	translate: function(){
	    var self = this;

	    KTApp.blockPage(blockOptions);

	    var formElement = self.formElement[0]; 
	    var formData = new FormData(formElement);

	    formData.append('file_to', $('#fileInput_to')[0].files[0]);
	    formData.append('file_from', $('#fileInput_from')[0].files[0]);

	    nhMain.callAjax({
	        async: true, 
	        url: adminPath + '/setting/translate-locale-process',
	        data: formData,
	        processData: false, 
	        contentType: false, 
	    }).done(function(response) {
	        KTApp.unblockPage();

	        var code = response.code || _ERROR;
	        var message = response.message || '';
	        var data = response.data || {};

	        var tmp = $('[nh-dowload-file]');
	        var url = 'javascript:;';
	        if (data.lang != _UNDEFINED) {
	            tmp.addClass('d-none');
	        }
	        if (data.type == 'js') {
	            url = `/tmp/locales_translate/${data.lang}.js`;
	            tmp.removeClass('d-none');
	        }
	        if (data.type == 'po') {
	            url = `/tmp/locales_translate/${data.lang}.po`;
	            tmp.removeClass('d-none');
	        }
	        tmp.attr("href", url);

	        if(response.code == _SUCCESS){
	            toastr.success(message);
	        }else{
	            toastr.error(message);
	        }
	    });
	}
};

$(document).ready(function() {
    nhTranslateLocale.init();
});