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

var nhListExtendData = function() {

	var columns = [];
	$.each(collectionFields, function(index, fieldInfo) {
		var code = fieldInfo.code || '';
		var name = fieldInfo.name || '';
		var inputType = fieldInfo.input_type || '';
		var view = fieldInfo.view || 0;

		if(code == '' || name == '' || inputType == '' || view == 0) return;

		var allowType = [
			'text', 
			'numeric', 
			'single_select',
			'multiple_select',
			'date', 
			'date_time', 
			'switch_input', 
			'image', 
			'images', 
			'video', 
			'files'
		];

		if($.inArray(inputType, allowType) == -1) return;
		var fieldOptions = fieldInfo.options || [];

		columns.push({
			field: code,
			title: name,
			autoHide: false,
			sortable: false,
			template: function(row){
				var value = row[code] || '';
				if(value == '' && inputType != 'switch_input') return '';

				switch(inputType){
					case 'single_select':												
						return fieldOptions[value] || '';
					break;

					case 'multiple_select':
						if(!nhMain.utilities.isJson(value)) return '';
						if(fieldOptions.length == 0) return '';

						var arrValues = $.parseJSON(value);
						var arrText = [];
						$.each(arrValues, function(i, valueItem) {
							if(typeof(fieldOptions[valueItem]) != _UNDEFINED) arrText.push(fieldOptions[valueItem]);
						});

						if(arrText.length == 0) return '';
						
						return arrText.join(', ');
					break;

					case 'switch_input':
						if(value == 1){
							return `
								<span class="kt-badge kt-badge--inline kt-badge--success">
									${nhMain.getLabel('co')}
								</span>`;
						}else{
							return `
								<span class="kt-badge kt-badge--inline kt-badge--danger">
									${nhMain.getLabel('khong')}									
								</span>`;
						}
					break;

					case 'image':
						return nhList.template.images([value]);
					break;

					case 'images':
						if(!nhMain.utilities.isJson(value)) return '';
						var images = $.parseJSON(value);

						return nhList.template.images(images);
					break;

					case 'video':
						if(!nhMain.utilities.isJson(value)) return '';
						var videoInfo = $.parseJSON(value);

						var url = videoInfo.url || '';
						var type = videoInfo.type || 'video_youtube';
						if(url == '') return '';

						if(type == 'video_youtube'){
							return `
								<a href="https://www.youtube.com/watch?v=${url}" target="_blank" class="btn btn-sm btn-secondary btn-icon">
									<i class="fab fa-youtube"></i>
								</a>`;
						}else{
							return `
								<a href="${cdnUrl}${url}" target="_blank" class="btn btn-sm btn-secondary btn-icon">
									<i class="fa fa-photo-video"></i>
								</a>`;
						}
					break;

					case 'files':
						if(!nhMain.utilities.isJson(value)) return '';
						var files = $.parseJSON(value);

						var htmlFile = '';
						$.each(files, function(index, file) {
							if(file == null || typeof(file) == _UNDEFINED || file.length == 0) return;

							var type = '';
							var ext = file.substring(file.lastIndexOf(".")).replace('.', '');
					        switch(ext){
					            case 'xlsx':
					            case 'xlsm':
					            case 'xls':
					                type = 'excel';
					            break;

					            case 'doc':
					            case 'docx':
					                type = 'word';
					            break;

					            case 'pdf':
					                type = 'pdf';
					            break;
					        }

							htmlFile += `
								<a href="${cdnUrl + file}" class="btn btn-sm btn-secondary btn-icon mr-1" target="_blank">
									<i class="fa fa-file-${type}">
			                    </a>`;
						});

						return `<div class="kt-user-card-v2 kt-user-card-v2--uncircle">${htmlFile}</div>`;
						
					break;

					case 'text':
					case 'numeric':
					case 'date':
					case 'date_time':
						return row[code] || '';
					break;

					default:
						return row[code] || '';
					break;
				}

				return '';
				
			}
		});		
	});

	// language column
	if(useMultipleLanguage){
		columns.push({
			field: 'lang',
			title: htmlListLang,
			class: useMultipleLanguage ? '' : 'd-none',
			sortable: false,
			textAlign: 'center',
			template: function(row) {
				var recordId = row.id || '';

				var mutiple_language = nhMain.utilities.notEmpty(row.mutiple_language) ? row.mutiple_language : [];
				var templateLanguage = '';
				var urlTranslate = `${adminPath}/extend-data/${collectionCode}/update/${recordId}`;
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
		});
	}

	// position column
	columns.push({
		field: 'status',
		title: nhMain.getLabel('trang_thai'),
		width: 110,
		autoHide: false,
		sortable: true,
		template: function(row) {
			return nhList.template.status(row.status);;

		},
	});

	// status column
	columns.push({
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
	});

	// action column
	columns.push({
		field: 'action',
		title: '',
		width: 30,
		autoHide: false,
		sortable: false,
		template: function(row){
			var recordId = row.id || '';
			return `
			<div class="dropdown dropdown-inline">
				<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">
					<i class="flaticon-more"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">
					<a class="dropdown-item" href="${adminPath}/extend-data/${collectionCode}/update/${recordId}">
						<span class="text-primary"><i class="fa fa-edit fs-14 mr-10"></i>
							${nhMain.getLabel('cap_nhat')}
						</span>
					</a>					
					<a class="dropdown-item nh-change-status" href="javascript:;" data-id="${recordId}" data-status="1">
						<span class="text-success"><i class="fas fa-check-circle fs-14 mr-10"></i>
							${nhMain.getLabel('hoat_dong')}
						</span>
					</a>
					<a class="dropdown-item nh-change-status" href="javascript:;" data-id="${recordId}" data-status="0">
						<span class="text-warning"><i class="fas fa-times-circle fs-14 mr-10"></i>
							${nhMain.getLabel('ngung_hoat_dong')}
						</span>
					</a>
					<a class="dropdown-item nh-delete" href="javascript:;" data-id="${recordId}">
						<span class="text-danger"><i class="fas fa-trash-alt fs-14 mr-10"></i>
							${nhMain.getLabel('xoa')}
						</span>
					</a>
				</div>
			</div>`;


		}
	});

	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: `${adminPath}/extend-data/${collectionCode}/list/json`,
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

		columns: columns
	};

	return {
		listData: function() {			     
			var datatable = $('.kt-datatable').KTDatatable(options);

		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
		    		status: `${adminPath}/extend-data/${collectionCode}/change-status`,
			    	delete: `${adminPath}/extend-data/${collectionCode}/delete`,
			    	quickChange: `${adminPath}/extend-data/${collectionCode}/change-position`
			    }
		    });

		    $('.kt-selectpicker').selectpicker();

		    lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });

            $('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});
		}
	};
}();

$(document).ready(function() {
	nhListExtendData.listData();
});

