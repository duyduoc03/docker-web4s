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
function getOptionsDataTable(){
	var columns = [
			{
				field: 'id',
				title: '',
				class: '',
				width: 18,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},			
			{
				field: 'name',
				title: nhMain.getLabel('ten_thuong_hieu'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/brand/update/' + row.id;
					var urlDetail = adminPath + '/brand/detail/' + row.id;

					var viewTemplate = ''
					if(url.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>' + viewTemplate + '\
								<span class="d-block kt-user-card-v2__desc action-entire">\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'image_avatar',
				title: '<div><i class="fa fa-image fa-lg"></i></div>',
				sortable: false,
				width: 155,
				template: function(row) {
					var imageAvatar = nhMain.utilities.notEmpty(row.image_avatar) ? row.image_avatar : '';
					var images = [];

					if(KTUtil.isset(row, 'has_album') && row.has_album != null){
						images = nhMain.utilities.notEmpty(row.images) ? JSON.stringify(row.images) : [];
					}

					return nhList.template.changeImage(row.id, images, imageAvatar);
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
					var templateLanguage = '';
					var urlTranslate = adminPath + '/brand/update/' + row.id;
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
				width: 60,
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
				field: 'type_video',
				title: nhMain.getLabel('Video'),
				sortable: true,
				textAlign: 'center',
				width: 90,
				template: function(row) {
					var url = row.url_video || '';
					var type = row.type_video || '';

					var html = '';
					if(type == 'video_youtube' && url.length > 0){
						html = `
							<a target="_blank" href="https://www.youtube.com/watch?v=${url}">
								<span class="kt-section__content kt-section__content--solid ">
									<i class="flaticon-youtube fs-20"></i>
								</span>
							</a>`;
					}

					if(type == 'video_system' && url.length > 0){
						html = `
							<a target="_blank" href="https:
							//${cdnUrl}${url}">
								<span class="kt-section__content kt-section__content--solid ">
									<span class="fa fa-video"></span>
								</span>
							</a>`;	
					}

					return html;
				}
			},
			{
				field: 'created_by',
				title: nhMain.getLabel('nguoi_tao'),
				width: 120,
				sortable: false,
				template: function(row) {
					var created_by_user = row.created_by_user || '';

					return created_by_user;
				},	
			},
			{
				field: 'created',
				title: nhMain.getLabel('ngay_tao'),
				width: 130,
				sortable: false,
				template: function(row) {
					return nhList.template.createdBy(row);
				}
			},
			{
				field: 'status',
				title: `<span>${nhMain.getLabel('trang_thai')} <span nh-btn="setting-field-view" class="fa fa-cog fs-13  ml-3 "> </span><span>` ,
				width: 110,
				sortable: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;					
				},
			}
	];

	var finalColumns = [];
	$('#setting-field-modal').find('input[type="checkbox"]:checked').each(function() {
		var name = $(this).attr('name').match(/\[([^\]]+)\]/)[1] || ''; 
		if(name == '') return;
		$.each(columns, function(index, col) {
		    if(col.field == name) {
		    	finalColumns.push(col);
		    }
		}); 
	});

	var statusColumn = finalColumns.find(col => col.field === 'status');
	if (statusColumn) {
		finalColumns = finalColumns.filter(col => col.field !== 'status');
		finalColumns.push(statusColumn);
	}
	finalColumns = finalColumns.length != 0 ? finalColumns : columns;

	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/brand/list/json',
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

		data_filter: {
			lang: nhMain.lang,
			keyword: $('#nh-keyword').val(),
			status: $('#nh-status').val()
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
			input: $('#nh-keyword')
		},

		translate: {
            records: {
                processing: nhMain.getLabel('vui_long_cho') +  ' ...',
                noRecords: nhMain.getLabel('khong_co_ban_ghi_nao'),
            }
        },

		columns: finalColumns
	};

	return options;
}

var nhListBrand = function() {
	
	return {
		listData: function() {
			var options = getOptionsDataTable();
			var datatable = $('.kt-datatable').KTDatatable(options);
			var supperAdmin = $('.kt-datatable').attr('nh-role') == 'supper-admin' ? true : false;
			if (supperAdmin) $('[nh-btn="setting-field-view"]').remove();

		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		 

		    $('#created_by').on('change', function() {
		      	datatable.search($(this).val(), 'created_by');
		    });	  		   
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/brand/delete',
			    	status: adminPath + '/brand/change-status',
			    	quickChange: adminPath + '/brand/change-position'
			    }
		    });		
		    $('.kt-selectpicker').selectpicker();    
		}
	};
}();

jQuery(document).ready(function() {
	nhListBrand.listData();
	nhSettingListField.init();
});

