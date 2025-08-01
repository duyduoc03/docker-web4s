"use strict";

var htmlListLang = '';
if(!$.isEmptyObject(listLanguage)){
	htmlListLang += '<div class="list-flags head-flags text-center">';
	$.each(listLanguage, function(code, name) {
		var flagDefault = '';
		if(nhMain.lang == code){
			flagDefault = 'flag-default';
		}
	  	htmlListLang += '<a href="?lang='+ code +'"><img src="'+ _FLAGS + code + '.svg" alt="'+ name +'" class="flag ' + flagDefault + '"></a>'
	});
	htmlListLang += '</div>';
}

var nhListUser = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/author/list/json',
					headers: {
						'X-CSRF-Token': csrfToken
					},
					map: function(raw) {
						var dataSet = raw;
						if (typeof raw.data !== _UNDEFINED) {
							dataSet = raw.data;
						}
						return dataSet;
					},
				},
			},
			pageSize: paginationLimitAdmin,
			serverPaging: true,
			serverFiltering: true,
			serverSorting: true,
		},
		
		layout: {
			scroll: false,
			footer: false,
			class: 'table-hover',
		},

		sortable: true,

		pagination: true,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-keyword'),
		},
		translate: {
            records: {
                processing: nhMain.getLabel('vui_long_cho') +  ' ...',
                noRecords: nhMain.getLabel('khong_co_ban_ghi_nao'),
            }
        },
		columns: [
			{
				field: 'id',
				title: '',
				width: 30,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
			},
			{
				field: 'full_name',
				title: nhMain.getLabel('ho_ten'),
				autoHide: false,
				width: 350,
				template: function(row) {
					var urlEdit = adminPath + '/author/update/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="kt-user-card-v2__name">'+ row.full_name +'</a>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			}, 
			{
				field: 'avatar',
				title: '<div class="text-center"><i class="fa fa-image fa-lg"></i></div>',
				sortable: false,
				width: 40,
				template: function(row) {
					var avatar = _NO_IMAGE;

					if(KTUtil.isset(row, 'avatar') && row.avatar != null){
						var imageAvatar = nhMain.utilities.notEmpty(row.avatar) ? nhMain.utilities.getThumbs(row.avatar, 150) : '';
						avatar = cdnUrl + imageAvatar;
					}
					
					var templateImage = '\
						<div class="symbol-group symbol-hover">\
							<a class="symbol symbol-circle" href="'+ avatar +'" data-lightbox="'+ row.id +'">\
								<img src="'+ avatar +'">\
							</a>';

					templateImage += '</div>';
					return templateImage;
				}
			},
			{
				field: 'lang',
				title: htmlListLang,
				class: useMultipleLanguage ? '' : 'd-none',
				sortable: false,
				textAlign: 'center',
				template: function(row) {
					var mutiple_language = nhMain.utilities.notEmpty(row.mutiple_language) ? row.mutiple_language : [];
					console.log(mutiple_language);
					var templateLanguage = '';
					var urlTranslate = adminPath + '/author/update/' + row.id;
					var templateLanguage = '<div class="list-flags">';
					$.each(listLanguage, function(code, name) {
						var flag_class = '';
						if(typeof(mutiple_language[code]) != _UNDEFINED && mutiple_language[code]){
							flag_class = 'text-primary';
						}
					  	templateLanguage += '<a href="'+ urlTranslate + '?lang=' + code +'" class="fa fa-pencil-alt flag ' + flag_class + '" title="'+ nhMain.getLabel('dich_sang') + ' ' + name +'">'
					});
					templateLanguage += '</div>';
					return templateLanguage;
				}
			}, 
			{
				field: 'position',
				title: nhMain.getLabel('vi_tri'),
				width: 90,
				sortable: true,
				textAlign: 'center',
				template: function (row) {
					var position = '';
					if(KTUtil.isset(row, 'position') && row.position != null){
						position = nhMain.utilities.parseNumberToTextMoney(row.position);
					}
					return nhList.template.changeQuick(row.id, 'position', position, nhMain.getLabel('vi_tri'));
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 90,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;	
				},
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);

		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });	   
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/author/delete',
			    	status: adminPath + '/author/change-status',
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListUser.listData();
});

