"use strict";

var nhMigrateLocationProcess = {
	tablesWrap: $('table[nh-table="tables"]'),
	fieldsWrap: $('table[nh-table="fields"]'),
	processWrap: $('[nh-wrap="process-form"]'),
	pauseProcess: false,
	init: function(){
		var self = this;
		self.events();
		self.loadTables();
	},
	events: function(){
		var self = this;
		
		$(document).on('click', '[nh-btn="load-fields"]', function(e) {
			e.preventDefault();

			var table = $(this).attr('nh-table') || '';

			self.loadFields(table);
			self.reloadProcessForm(table);
		});

		$(document).on('click', '[nh-btn="check-status"]', function(e) {
			var table = $(this).attr('data-table') || '';
			self.checkStatus(table);
		});

		$(document).on('click', '[nh-btn="processing"]:not(.disabled)', function(e) {
			var table = $(this).attr('data-table') || '';
			var fields = $(this).attr('data-fields') || '';

			self.pauseProcess = false;

			self.processMigrate(table, fields);
		});

		$(document).on('click', '[nh-btn="pause"]:not(.disabled)', function(e) {
			self.pauseProcess = true;
		});

	},
	loadTables: function(){
		var self = this;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-tables',
            dataType : 'html'
        }).done(function(response){
            self.tablesWrap.find('tbody').html(response);
            self.fieldsWrap.find('tbody').html('');
            self.processWrap.html('');
        });
	},
	loadFields: function(table = ''){
		var self = this;

		if(self.fieldsWrap.length == 0) return;
		if(table == '') return;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-fields',
            data: {
            	table: table
            },
            dataType : 'html'
        }).done(function(response){
            self.fieldsWrap.find('tbody').html(response);
        });
	},
	reloadProcessForm: function(table = ''){
		var self = this;

		if(self.processWrap.length == 0) return;
		if(table == '') return;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/reload-process-form',
            data: {
            	table: table
            },
            dataType : 'html'
        }).done(function(response){
            self.processWrap.html(response);
        });
	},
	checkStatus: function(table = ''){
		var self = this;

		if(self.processWrap.length == 0) return;
		if(table == '') return;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/check-status',
            data: {
            	table: table
            },
        }).done(function(response){
            var code = response.code || _ERROR;
        	var message = response.message || '';
        	var data = response.data || {};
        	
            if (code == _SUCCESS) {
            	var fieldSynced = data.field_synced || false;
	        	var syncedRecord = data.synced_record || 0;
	        	var totalRecord = data.total_record || 0;
	        	var fields = data.fields || [];
	        	var htmlFieldSynced = '<span class="text-danger">Chưa tạo</span>';
	        	if(fieldSynced){
	        		htmlFieldSynced = '<span class="text-success">Đã tạo</span>';
	        		$('[nh-btn="processing"]').removeClass('disabled');
	        		$('[nh-btn="pause"]').removeClass('disabled');
	        	}

	        	self.processWrap.find('[nh-label="field-synced"]').html(htmlFieldSynced);
	        	self.processWrap.find('[nh-label="synced-record"]').text(syncedRecord);
	        	self.processWrap.find('[nh-label="total-record"]').text(totalRecord);

	        	self.processWrap.find('[nh-btn="processing"]').attr('data-fields', JSON.stringify(fields));
            } else {
            	toastr.error(message);
            }
        });
	},
	processMigrate: function(table = '', fields = ''){
		var self = this;

		if(self.processWrap.length == 0) return;
		if(table == '') return;

		if(self.pauseProcess) return;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/processing',
            data: {
            	table: table,
            	fields: fields
            },
        }).done(function(response){
            var code = response.code || _ERROR;
        	var message = response.message || '';
        	var data = response.data || {};        	
        	var numberRecord = data.number_record || 0;
        	var synced = self.processWrap.find('[nh-label="synced-record"]').text() || 0;
        	synced = parseInt(synced) + numberRecord;

        	var total = self.processWrap.find('[nh-label="total-record"]').text() || 0;
        	total = parseInt(total);

        	var percent = 0;
        	if(synced > 0) percent = parseInt(synced / total * 100);

        	if(percent > 100) percent = 100;
        	if(percent < 0) percent = 0;
        	percent = Math.round(percent);

            if (code == _SUCCESS) {
            	
	        	if(numberRecord == 0 || synced >= total){
        			self.progressBar(100, 'success', 100);
        			toastr.success(message);
        		}else{
        			self.progressBar(percent);
        			self.processMigrate(table, fields);        			
        		}

        		self.processWrap.find('[nh-label="synced-record"]').text(synced);

            } else {
            	self.progressBar(progressBar, 'error', percent);
            	toastr.error(message);
            }
        });
	},
	progressBar: function(percent = 0, status = 'processing'){
		var self = this;

		var element = $('[nh-wrap="progress"]');
		if(element.length == 0) return;
		if(element.find('.progress-bar').length == 0) return;

		element.find('.progress-bar').css('width', percent + '%');
		if(status == 'success') {
			element.find('.progress-bar').addClass('bg-success').removeClass('bg-danger');
		}

		if(status == 'error'){
			element.find('.progress-bar').addClass('bg-danger').removeClass('bg-success');
		}
	}
}

$(document).ready(function() {
	nhMigrateLocationProcess.init();
});

