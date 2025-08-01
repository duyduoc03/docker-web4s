"use strict";

var nhLogRecord = {
	btnElement: $('[nh-btn="show-log-tab"]'),
	tabElement: $('[nh-tab="log-tab"]'),
	modalElement: $('#log-modal'),
	init: function(){
		var self = this;

		if(self.btnElement.length == 0 || self.tabElement.length == 0 || self.modalElement.length == 0) return;
		self.events();
	},
	events: function(){
		var self = this;
		
		self.btnElement.on('show.bs.tab', function (event) {
		  	self.loadInitViewTab();
		});

		// ================================= events in table logs

		self.tabElement.on('click', '[nh-page]', function(e) {
			var page = $(this).attr('nh-page');
			self.loadInitViewTab(page);
		});

		self.tabElement.on('click', '[nh-btn="show-change-data"]', function(e) {
			e.preventDefault();

			var version = $(this).attr('version') || '';
			var recordId = self.tabElement.attr('record-id') || '';
			var subType = self.tabElement.attr('sub-type') || '';
			var device = self.tabElement.attr('data-device') || 0;

			if(version == '' || recordId == '' || subType == '') return;

	        nhMain.callAjax({
	            async: true,
	            url: adminPath + '/log/load-change-data',
	            data:{
	            	record_id: recordId,
	            	sub_type: subType,
	            	version : version,
	            	device: device
	            },
	            dataType : 'html'
	        }).done(function(response){
	            self.modalElement.find('.modal-body').html(response);
	        });


			self.modalElement.modal('show');
		});

		self.tabElement.on('click', '[nh-btn="show-data-version"]', function(e) {
			e.preventDefault();

			var version = $(this).attr('version') || '';
			var recordId = self.tabElement.attr('record-id') || '';
			var recordCode = self.tabElement.attr('record-code') || '';
			var subType = self.tabElement.attr('sub-type') || '';
			var device = self.tabElement.attr('data-device') || 0;

			if(version == '' || subType == '') return;

			nhMain.callAjax({
	            async: true,
	            url: adminPath + '/log/load-data-by-version',
	            data:{
	            	record_id: recordId,
	            	record_code: recordCode,
	            	sub_type: subType,
	            	version : version,
	            	device: device
	            },
	            dataType : 'html'
	        }).done(function(response){
	            self.modalElement.find('.modal-body').html(response);
	        });

	        self.modalElement.modal('show');
		});

		// ================================= events in modal log

		self.modalElement.on('click', '[nh-btn="show-change-field"]', function(e) {
			e.preventDefault();

			var beforeText = $(this).closest('tr').find('[nh-log-field="before"]').text() || '';
			var afterText = $(this).closest('tr').find('[nh-log-field="after"]').text() || '';
			if(beforeText == '' || afterText == '') return;
			beforeText = beforeText.replaceAll(/\s/g,'');
			afterText = afterText.replaceAll(/\s/g,'');
			
			var dmpObject = new diff_match_patch();
			var different = dmpObject.diff_main(beforeText, afterText);
			dmpObject.diff_cleanupSemantic(different);

			var result = dmpObject.diff_prettyHtml(different);
			$(this).closest('tr').find('[nh-log-field="after"]').html(result);
		});
		
		self.modalElement.on('click', '[nh-btn="rollback-log"]', function(e) {
			var version = $(this).attr('version') || '';
			var recordId = self.tabElement.attr('record-id') || '';
			var recordCode = self.tabElement.attr('record-code') || '';
			var subType = self.tabElement.attr('sub-type') || '';
			var device = self.tabElement.attr('data-device') || 0;
			console.log(device);
			if(recordId == '' && recordCode == '') return;
			if(version == '' || subType == '') return;

			var url = '';
			switch(subType) {
			  	case 'article':
			    	url = `${adminPath}/article/rollback-log`;
			    break;

			  	case 'product':
					url = `${adminPath}/product/rollback-log`;
			    break;

				case 'category':
					url = `${adminPath}/category/rollback-log`;
			    break;

				case 'brand':
					url = `${adminPath}/brand/rollback-log`;
			    break;
			    case 'author':
					url = `${adminPath}/author/rollback-log`;
			    break;
			    case 'block':
					url = `${adminPath}/template/block/rollback-log`; 
			    break;
			    case 'template_page':
					url = `${adminPath}/template/rollback-log`; 
			    break;
			}

			if(url == '') return;

			swal.fire({
		        title: nhMain.getLabel('quay_lai_ban_ghi_cu'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_thuc_hien'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="fa fa-undo"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',
		        
		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		nhMain.callAjax({
			            async: true,
			            url: url,
			            data:{
			            	record_id: recordId,
			            	record_code: recordCode,
			            	version : version,
			            	device: device
			            },
			        }).done(function(response){
			            var code = response.code || _ERROR;
			        	var message = response.message || '';
			        	toastr.clear();

			            if (code == _SUCCESS) {
			            	toastr.info(message);

			            	location.reload();
			            } else {
			            	toastr.error(message);
			            }
			        });
		    	}    	
		    });

			
		});

		self.modalElement.on('hide.bs.modal', function (e) {
		  	self.modalElement.find('.modal-body').html('');
		})		
	},
	loadInitViewTab: function(page = 1){
		var self = this;

		var loaded = self.tabElement.hasClass('loaded') || false;
		var recordId = self.tabElement.attr('record-id') || '';
		var recordCode = self.tabElement.attr('record-code') || '';
		var subType = self.tabElement.attr('sub-type') || '';

		if(subType == '') return;

        nhMain.callAjax({
            async: true,
            url: adminPath + '/log/list-log-data-by-record',
            data:{
            	record_id: recordId,
            	record_code: recordCode,
            	sub_type: subType,
            	page : page
            },
            dataType : 'html'
        }).done(function(response){
            self.tabElement.html(response);
            self.tabElement.addClass('loaded');
        });
	}	
}

$(document).ready(function() {
	nhLogRecord.init();
});
