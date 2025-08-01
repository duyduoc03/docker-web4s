"use strict";

var nhListLadipage = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/ladipage/list/json',
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

		columns: [
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
				title: 'Tên landing',
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/ladipage/update/' + row.id;
					var urlDetail = '/' + url;

					var viewTemplate = ''
					if(url.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="d-inline kt-user-card-v2__name" target="_blank">'+ name +'</a>' + viewTemplate + '\
								<span class="d-block kt-user-card-v2__desc action-entire">\
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
				field: 'created',
				title: 'Ngày tạo',
				width: 150,
				sortable: true,
				textAlign: 'center',
				template: function (row) {
					var created = '';
					if(KTUtil.isset(row, 'created') && row.created != null){
						created = row.created;
					}
					return created;
				}
			},
			{
				field: 'updated',
				title: 'Ngày cập nhật',
				width: 150,
				sortable: true,
				textAlign: 'center',
				template: function (row) {
					var updated = '';
					if(KTUtil.isset(row, 'updated') && row.updated != null){
						updated = row.updated;
					}
					return updated;
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
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
		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/ladipage/delete',
			    	status: adminPath + '/ladipage/change-status'
			    }
		    });		
		    $('.kt-selectpicker').selectpicker(); 


		    $(document).on('click', '.btn-publish-lp', function(e) {
				e.preventDefault();

				var ladipage_key = $('[name="ladipage_key"]').val();

				if(ladipage_key) {
					nhMain.callAjax({
			    		async: false,
						url: $(this).closest('form').attr('action'),
			    		data: {
			    			ladipage_key: ladipage_key
			    		}
					}).done(function(response) {
						var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			        	if (code == '200') {	                    
		                    $('[name="ladipage_key"]').val('');
		                    toastr.info(message);
			            	$('.kt-datatable').KTDatatable('reload');
			            	$('#modal-publish-lp').modal('hide');
			            } else {
			            	toastr.error(message);
			            }   
					});
				}
			});   
		}
	};
}();

jQuery(document).ready(function() {
	nhListLadipage.listData();
});

