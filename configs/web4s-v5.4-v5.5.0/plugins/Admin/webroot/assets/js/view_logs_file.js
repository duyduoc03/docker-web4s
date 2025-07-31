"use strict";


var nhViewLogFile = {
	modalElement: $('#log-file-modal'),
	btnViewElement: null,
	pathFile: '',
	init: function(){
		var self = this;

		self.btnViewElement = $('[nh-btn="view-history-change-file"]');
		
		if(self.btnViewElement.length == 0) return;

		self.events();
	},	
	events: function(){
		var self = this;

		self.btnViewElement.click(function(e) {
			var path = $(this).attr('data-path') || '';

			if(path == ''){
				toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
				return;
			}

			window.open(`${adminPath}/template/modify/log?file=${btoa(path)}`, '_blank');
		});
	}
}

$(document).ready(function() {
	nhViewLogFile.init();
});
