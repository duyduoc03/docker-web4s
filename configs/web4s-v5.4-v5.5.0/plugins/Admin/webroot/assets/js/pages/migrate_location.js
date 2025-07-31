"use strict";

var nhMigrateLocation = {
	citiesTable: $('table[nh-table="cities"]'),
	districtsTable: $('table[nh-table="districts"]'),
	wardsTable: $('table[nh-table="wards"]'),
	migrateModal: $('#merge-city-modal'),
	init: function(){
		var self = this;

		self.loadCities();

		self.events();
	},
	events: function(){
		var self = this;

		$(document).on('click', '[nh-btn="import-data"]:not(.disabled)', function(e) {
			var btnImport = $(this);

			// show loading
			// btnImport.find('.icon-spinner').removeClass('d-none');
			// btnImport.addClass('disabled');
		

			var inputFile = $('input[name="excel_file"]');
            var files = inputFile[0].files;
            if(inputFile.length == 0){
				nhMain.showLog(nhMain.getLabel('khong_tim_thay_du_lieu_file'));
			}

			var formData = new FormData();
            $.each(files, function(index, file) {
				formData.append('excel_file', file);
			});
           
            nhMain.callAjax({
	    		async: true,
				url: adminPath + '/migrate-location/import-data',
				data: formData,
				contentType: false,
				processData: false,
			}).done(function(response) {
				KTApp.unblockPage();

				var code = response.code || _ERROR;
	        	var message = response.message || '';
	        	var data = response.data || {};

	        	if (code == _SUCCESS) {
	        		toastr.success(message);
	        		btnImport.find('.icon-spinner').addClass('d-none');
					$(this).removeClass('disabled');
	        	}else{
	            	toastr.error(message);
	        	}
			});

			return false;
		});
		
		$(document).on('click', '[nh-btn="load-list-districts"]', function(e) {
			e.preventDefault();

			var cityId = $(this).attr('city-id') || '';
			var migrateCityId = $(this).attr('migrate-city-id') || '';

			self.citiesTable.find('tr').removeClass('active');
			$(this).closest('tr').addClass('active');

			self.districtsTable.attr('from-city', cityId);
			self.districtsTable.attr('from-city-migrate', migrateCityId);

			self.loadDistricts(cityId, migrateCityId);
		});

		$(document).on('click', '[nh-btn="load-list-wards"]', function(e) {
			e.preventDefault();

			var districtId = $(this).attr('district-id') || '';
			var migrateDistrictId = $(this).attr('migrate-district-id') || '';

			self.districtsTable.find('tr').removeClass('active');
			$(this).closest('tr').addClass('active');

			self.wardsTable.attr('from-district', districtId);
			self.wardsTable.attr('from-district-migrate', migrateDistrictId);

			self.loadWards(districtId, migrateDistrictId);
		});

		$(document).on('click', '[nh-btn="show-migrate-modal"]', function(e) {
			var object = $(this).attr('data-object') || '';
			var id = $(this).attr('data-id') || '';
			var migrateId = $(this).attr('migrate-id') || '';


			self.showModalMigrate(object, id, migrateId);
		});

		self.migrateModal.on('click', '[nh-btn="migrate-location"]', function(e) {
			var formElement = self.migrateModal.find('form#merge-form');
			if(formElement.length == 0) return;

			KTApp.blockPage(blockOptions);

			var object = formElement.find('input[name="object"]').val() || '';

			var formData = formElement.serialize();
			nhMain.callAjax({
				url: formElement.attr('action'),
				data: formData
			}).done(function(response) {
				KTApp.unblockPage();

			   	var code = response.code || _ERROR;
	        	var message = response.message || '';
	        	var data = response.data || {};
	        	toastr.clear();

	            if (code == _SUCCESS) {
	            	toastr.info(message);

	            	self.migrateModal.modal('hide');
	            	switch(object){
	            		case 'city':
	            			self.loadCities();
	            		break;

	            		case 'district':
	            			self.loadDistricts();
	            		break;

	            		case 'ward':
	            			self.loadWards();
	            		break;
	            	}
	            } else {
	            	toastr.error(message);
	            }	            
			});
		});
		
		self.migrateModal.on('change', 'select[name="type"]', function(e) {
			var type = $(this).val() || '';
			if(type == 'merge'){
				$('[nh-wrap="update"]').addClass('d-none');
				$('[nh-wrap="merge"]').removeClass('d-none');
			}else{
				$('[nh-wrap="update"]').removeClass('d-none');
				$('[nh-wrap="merge"]').addClass('d-none');
			}
		});

		nhMain.location.init({
			idWrap: ['#merge-form']
		});

		$(document).on('click', '[nh-btn="reload-list"]', function(e) {
			var type = $(this).attr('data-type') || '';

			if(type == 'city') self.loadCities();
			if(type == 'district') self.loadDistricts();
			if(type == 'ward') self.loadWards();
		});
	},
	loadCities: function(){
		var self = this;

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-cities',
            dataType : 'html'
        }).done(function(response){
            self.citiesTable.find('tbody').html(response);

            self.districtsTable.find('tbody').html('');
            self.wardsTable.find('tbody').html('');

            self.showCompare(self.citiesTable);
        });
	},
	loadDistricts: function(city_id = '', migrate_city_id = ''){
		var self = this;

		if(city_id == '') city_id = self.districtsTable.attr('from-city') || '';
		if(migrate_city_id == '') migrate_city_id = self.districtsTable.attr('from-city-migrate') || '';

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-districts',
            data: {
            	city_id: city_id,
            	migrate_city_id: migrate_city_id
            },
            dataType : 'html'
        }).done(function(response){
            self.districtsTable.find('tbody').html(response);
            self.wardsTable.find('tbody').html('');

            self.showCompare(self.districtsTable);
        });
	},
	loadWards: function(district_id = '', migrate_district_id = ''){
		var self = this;

		if(district_id == '') district_id = self.wardsTable.attr('from-district') || '';
		if(migrate_district_id == '') migrate_district_id = self.wardsTable.attr('from-district-migrate') || '';

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-wards',
            data: {
            	district_id: district_id,
            	migrate_district_id: migrate_district_id
            },
            dataType : 'html'
        }).done(function(response){
            self.wardsTable.find('tbody').html(response);

            self.showCompare(self.wardsTable);
        });
	},
	showCompare: function(tableElement = ''){
		var self = this;

		if(typeof(tableElement) == _UNDEFINED || tableElement.length == null || tableElement.length == 0) return;

		tableElement.find('tbody tr').each(function() {
        	var orginText = $(this).find('div[nh-text="origin"]').html() || '';
			var migrateText = $(this).find('div[nh-text="migrate"]').html() || '';
			// if(orginText == '' || migrateText == '') return;

			orginText = orginText.replace(/(\r\n|\n|\r)/gm, '').trim();
			migrateText = migrateText.replace(/(\r\n|\n|\r)/gm, '').trim();

			var dmpObject = new diff_match_patch();
			var different = dmpObject.diff_main(orginText, migrateText);
			dmpObject.diff_cleanupSemantic(different);

			var differentHtml = dmpObject.diff_prettyHtml(different);
			$(this).find('div[nh-text="origin"]').html(differentHtml);
        });
	},
	showModalMigrate: function(object = '', recordId = '', migrateId = ''){
		var self = this;

		if(object == '' && recordId == '') return;

		self.migrateModal.find('.modal-body').html('');
		self.migrateModal.modal('show');

		var parentId = '';
		var migrateParentId = '';
		if(object == 'district'){
			parentId = $('[nh-table="districts"]').attr('from-city') || '';
			migrateParentId = $('[nh-table="districts"]').attr('from-city-migrate') || '';
		}

		if(object == 'ward'){
			parentId = $('[nh-table="wards"]').attr('from-district') || '';
			migrateParentId = $('[nh-table="wards"]').attr('from-district-migrate') || '';
		}

		nhMain.callAjax({
            async: true,
            url: adminPath + '/migrate-location/load-content-migrate-modal',
            dataType : 'html',
            data: {
            	object: object,
            	record_id: recordId,
            	migrate_id: migrateId,
            	parent_id: parentId,
            	migrate_parent_id: migrateParentId
            }
        }).done(function(response){
            self.migrateModal.find('.modal-body').html(response);
            self.migrateModal.find('select').selectpicker();
        });
	}
}

$(document).ready(function() {
	nhMigrateLocation.init();
});

