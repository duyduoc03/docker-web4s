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

function getOptionsDataTable()
{
	// tất cả các column của bài viết
	var columns = [
		{
			field: 'id',
			title: '',
			width: 18,
			type: 'number',
			selector: {class: 'select-record kt-checkbox bg-white'},
			textAlign: 'center',
			autoHide: false,
			sortable: false
		},
		{
				field: 'name',
				title: nhMain.getLabel('ten_vong_quay'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = row.name || '';
					var url = row.url || '';
					var urlEdit = adminPath + '/wheel-fortune/update/' + row.id;
					var urlDetail = adminPath + '/wheel-fortune/detail/' + row.id;

					var viewTemplate = ''
					if(url.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>' + viewTemplate + '\
								<span class="d-block kt-user-card-v2__desc action-entire">\
									<a href="'+ urlEdit +'" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
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
				var urlTranslate = adminPath + '/wheel-fortune/update/' + row.id;
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
            field: 'type',
            title: nhMain.getLabel('so_lieu_thong_ke'),
            sortable: false,
            width: 200,
            template: function(row) {
                var html = `
                    <a href="javascript://" class="mr-20" nh-statistics data-id="${row.id}">
                        <i class="fa fa-chart-bar"></i>
                        ${nhMain.getLabel('so_lieu')}
                    </a>
                    <a href="javascript://" class="mr-5" nh-export data-id="${row.id}">
                        <i class="fa fa-file-excel"></i>
                        ${nhMain.getLabel('xuat_excel')}
                    </a>`;

                return html;
            }
        },
		{
			field: 'winning_chance',
			title: nhMain.getLabel('co_hoi_trung_giai') + '(%)',
			sortable: true,
			textAlign: 'center',
			width: 130,
			template: function(row) {
				var  winning_chance = row.winning_chance != 0 ? row.winning_chance : '';
				return winning_chance;
			},			
		},
		{
			field: 'time_event',
			title: nhMain.getLabel('thoi_gian_ap_dung'),
			sortable: true,
			textAlign: 'center',
			width: 170,
			template: function(row) {
				var start_time = KTUtil.isset(row, 'start_time') && row.start_time != null ? nhMain.utilities.parseIntToDateString(row.start_time) + ' - ' : '';
				var end_time = KTUtil.isset(row, 'end_time') && row.end_time != null ? nhMain.utilities.parseIntToDateString(row.end_time) : '';
				
				return  start_time + end_time;
			},
		},
		{
			field: 'created',
			title: nhMain.getLabel('ngay_tao'),
			width: 130,
			sortable: false
		},
		{
			field: 'status',
			title: `<span>${nhMain.getLabel('trang_thai')}` ,
			width: 110,
			sortable: false,
			autoHide: false,
			template: function(row) {
				var status = '';
				var draftArticle = '';
				if(KTUtil.isset(row, 'status') && row.status != null){
					status = nhList.template.statusProduct(row.status);
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
					url: adminPath + '/wheel-fortune/list/json',
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
		visible: false,
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
	    columns: finalColumns
	};

	return options;
}


var nhListWheelFortune = function() {	
	return {
		listData: function() {
			
			var options = getOptionsDataTable();
			var datatable = $('.kt-datatable').KTDatatable(options);

			var supperAdmin = $('.kt-datatable').attr('nh-role') == 'supper-admin' ? true : false;
			if (supperAdmin) $('[nh-btn="setting-field-view"]').remove();
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/wheel-fortune/delete',
			    	status: adminPath + '/wheel-fortune/change-status',
			    	duplicate: adminPath + '/wheel-fortune/duplicate',
			    	quickChange: adminPath + '/wheel-fortune/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();

            $('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});

  			$(document).on('click', '[nh-statistics]', function(e) {
				e.preventDefault();

				var modalStatistics = $('#modal-statistics');
				if(modalStatistics.length == 0) return false;

				var wheel_id = $(this).data('id') || '';
				if(wheel_id == '') toastr.error(nhMain.getLabel('du_lieu_khong_hop_le'));

				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: adminPath + '/wheel-fortune/statistics/' + wheel_id,
					dataType: _HTML
				}).done(function(response) {
					KTApp.unblockPage();
					modalStatistics.find('.modal-body').html(response);
					modalStatistics.modal('show');
				});
			});

			$(document).on('click', '[nh-export]', function(e) {
				e.preventDefault();

				var wheel_id = $(this).data('id') || '';
				if(wheel_id == '') toastr.error(nhMain.getLabel('du_lieu_khong_hop_le'));

				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: adminPath + '/wheel-fortune-log/list/json',
					data: {
						'data_filter': {
							lang: nhMain.lang,
							wheel_id: wheel_id
						},
						'export': 'all'
					}
				}).done(function(response) {
					KTApp.unblockPage();
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					var message = typeof(response.message) != _UNDEFINED ? response.message : '';

					if (code == _SUCCESS) {
						var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

						var $tmp = $("<a>");
			            $tmp.attr("href",response.data);
			            $("body").append($tmp);
			            $tmp.attr("download", name + '.xlsx');
			            $tmp[0].click();
			            $tmp.remove();
		            	toastr.info(message);
		            } else {
		            	toastr.error(message);
		            }
				});
		
				return false;
			});
		}
	};
}();

$(document).ready(function() {
	nhSettingListField.init();
	nhListWheelFortune.listData();
});

