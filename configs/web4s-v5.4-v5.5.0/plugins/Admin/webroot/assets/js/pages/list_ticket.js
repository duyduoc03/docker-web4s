"use strict";

var nhListUser = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/ticket/list/json',
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
        
		columns: [
			{
				field: 'code',
				title: nhMain.getLabel('ma_ticket'),
				width: 100,
				template: function(row) {
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? '#' + row.code : '';
					return code;	
				}
			},
			{
				field: 'title',
				title: nhMain.getLabel('tieu_de'),
				autoHide: false,
				width: 300,
				template: function(row) {
					var urlDetail = adminPath + '/ticket/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ row.title +'</a>\
							</div>\
						</div>';
				}
			},
			{
				field: 'department_name',
				title: nhMain.getLabel('phong_ban'),
				width: 120,
				sortable: true
			},
			{
				field: 'priority_name',
				title: nhMain.getLabel('muc_do'),
				width: 100,
				sortable: true,
				template: function(row) {
                    var priorities = {
                        'low': {
                            'title': nhMain.getLabel('thap'),
                            'state': 'primary'
                        },
                        'medium': {
                            'title': nhMain.getLabel('trung_binh'),
                            'state': 'warning'
                        },
                        'high': {
                            'title': nhMain.getLabel('cao'),
                            'state': 'danger'
                        },
                    };
                    
                    if (typeof(priorities[row.priority]) != _UNDEFINED) {
                    	return '<span class="kt-badge kt-badge--' + priorities[row.priority].state + ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' + priorities[row.priority].state + '">' + priorities[row.priority].title + '</span>';
                    }

                    return row.priority;
                },
			},
			{
				field: 'updated',
				title: nhMain.getLabel('cap_nhat'),
				width: 140,
				sortable: true,
				template: function(row) {
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? nhMain.utilities.parseIntToDateTimeString(row.created) : '';
					return created;	
				},
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				textAlign: 'center',
				width: 90,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.statusTicket(row.status);
					}
					return status;	
				},
			},
			{
                field: '',
                title: '',
                sortable: false,
                width: 110,
                overflow: 'visible',
                autoHide: false,
                textAlign: 'center',
                template: function(row) {
                	var urlDetail = adminPath + '/ticket/detail/' + row.id;

                    return '\
						<a href="'+ urlDetail +'" class="btn kt-badge kt-badge--unified-info kt-font-bold kt-badge--inline kt-badge--pill" title="'+ nhMain.getLabel('chi_tiet') +'">\
							<i class="fa fa-info-circle"></i>\
							'+ nhMain.getLabel('xem_chi_tiet') +'\
						</a>\
					';
                }
            }]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });	

		    $('#department').on('change', function() {
		      	datatable.search($(this).val(), 'department');
		    });	

		    $('#priority').on('change', function() {
		      	datatable.search($(this).val(), 'priority');
		    });		   		   
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	status: adminPath + '/ticket/change-status',
			    	delete: adminPath + '/ticket/delete'
			    }
		    });

			$('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListUser.listData();
});

