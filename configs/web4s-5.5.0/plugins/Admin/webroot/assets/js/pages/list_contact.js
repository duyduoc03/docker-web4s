"use strict";

var nhListContact = function() {
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
		}
	];

	var numberView = 0;
	$.each(fields, function(index, fieldInfo) {
		if(fieldInfo.view > 0) numberView ++;
	});

	$.each(fields, function(index, fieldInfo) {
		var code = fieldInfo.code || '';
		var name = fieldInfo.label || '';
		var view = fieldInfo.view || 0;

		if(numberView == 0 && index < 4) view = 1;
		if(code == '' || name == '' || view == 0) return;

		columns.push({
			field: code,
			title: name,
			autoHide: false,
			sortable: false,
			template: function(row){
				var fieldValue = KTUtil.isset(row, 'value') && row.value != null ? row.value : [];
				var value = KTUtil.isset(fieldValue, code) && fieldValue[code] != null ? fieldValue[code] : '';

				var checkArr = $.isArray(value);
				if(checkArr) {
					var list_value = '';
					$.each(value, function(index, val) {
						list_value += val + '<br>';
					});

					value = list_value;
				}

				return value || '';
			}
		});
	
	});

	// nguon lien he
	columns.push({			
		field: 'tracking_source',
		title: nhMain.getLabel('nguon'),
		width: 100,
		template: function(row) {
			var tracking_source = KTUtil.isset(row, 'tracking_source') && row.tracking_source != null ? row.tracking_source : '';

			return tracking_source;
		}
	});

	// created column
	columns.push({
		field: 'created',
		title: nhMain.getLabel('ngay_nhan'),
		width: 130,
		template: function(row) {
			if(KTUtil.isset(row, 'created') && row.created != null){
				return row.created;
			}
		},
	});

	// position column
	columns.push({
		field: 'status',
		title: nhMain.getLabel('trang_thai'),
		width: 110,
		autoHide: false,
		sortable: true,
		template: function(row) {
			var status = '';
			var statusOptions = {
				0: {'title': nhMain.getLabel('chua_doc'), 'class': 'kt-badge--dark kt-font-bold'},
				2: {'title': nhMain.getLabel('chua_doc'), 'class': 'kt-badge--dark kt-font-bold'},
				1: {'title': nhMain.getLabel('da_doc'), 'class': 'kt-badge--success kt-font-bold'},
			};
			
			if(KTUtil.isset(row, 'status') && row.status != null){
				status = '<span contact-status="'+row.id+'" class="kt-badge ' + statusOptions[row.status].class + ' kt-badge--inline kt-badge--pill">' + statusOptions[row.status].title + '</span>';
			}
			return status;
		},
	});

	// action column
	columns.push({
		field: 'detail',
		title: '',
		width: 120,
		sortable: false,
		template: function(row){
			var result = '<a href="javascript:;" view-detail="'+row.id+'" data-toggle="modal" data-target="#modal-detail-contact">\
				<i class="fa fa-eye"></i>\
				'+ nhMain.getLabel('xem_chi_tiet') +'\
			</a>';
			return result;
		}
	});	

	

	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/contact/list/json',
					headers: {
						'X-CSRF-Token': csrfToken
					},
					map: function(raw) {
						var dataSet = raw;
						var field = null;
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
			status: $('#nh-status').val(),
			form_id: $('#form_id').val(),
			create_from: $('#create_from').val(),
			create_from: $('#create_from').val()
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
			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});
  			
			var datatable = $('.kt-datatable').KTDatatable(options);
			$('#tracking_source').on('keyup', function() {
		      	datatable.search($(this).val(), 'tracking_source');
		    });

		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    var tagify = new Tagify(document.getElementById('tracking_source'), {
	            pattern: /^.{0,45}$/,
	            delimiters: ", ",
	            maxTags: 10,
	            whitelist: ["Direct" ,"Website", "Google", "Shopee", "Tiki", "Lazada", "Zalo", "Facebook", "Mobile App", "Nguồn khác"],
	            dropdown: {
		            maxItems: 20,           // <- mixumum allowed rendered suggestions
		            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
		            enabled: 0,             // <- show suggestions on focus
		            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
		        }
	        });

	        tagify.on('change', function(e){
	        	var value = $('#tracking_source').val() || '';
	        	if(value != '') value = $.parseJSON(value);

	        	datatable.search(value, 'tracking_source');
			});

		    $('[change-form]').on('click', function() {
		    	var form_id = $(this).data('id');
		      	nhMain.callAjax({
					url: adminPath + '/contact/list',
					data: {
						form_id: form_id
					}
				}).done(function(response) {
					KTApp.unblockPage();
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					var message = typeof(response.message) != _UNDEFINED ? response.message : '';

					if (code == _SUCCESS) {
		            	location.reload();
		            } else {
		            	toastr.error(message);
		            }
				});
		    });	
		    
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/contact/delete',
			    	status: adminPath + '/contact/change-status',
			    }
		    });

			$('.kt-selectpicker').selectpicker();

            $(document).on('click', '[nh-export]', function(e) {
				e.preventDefault();
				var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: adminPath + '/contact/list/json',
					data: {
						'data_filter': {
							lang: nhMain.lang,
							keyword: $('#nh-keyword').val(),
							status: $('#nh_status').val(),
							form_id: $('#form_id').val(),
							create_from: $('#create_from').val(),
							create_from: $('#create_from').val()
						},
						'export': nhExport
					}
				}).done(function(response) {
					KTApp.unblockPage();
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					var message = typeof(response.message) != _UNDEFINED ? response.message : '';
					var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

					var $tmp = $("<a>");
		            $tmp.attr("href",response.data);
		            $("body").append($tmp);
		            $tmp.attr("download", name + '.xlsx');
		            $tmp[0].click();
		            $tmp.remove();

					if (code == _SUCCESS) {
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

var viewModalDetail = {
	wrapModalDetail: $('#modal-detail-contact'),
	init: function(){
		var self = this;

		if(self.wrapModalDetail.length == 0) return false;

		self.event();
	},
	event: function () {
		var self = this;
		var hash = window.location.hash;
		var id = hash.split('=')[1];
		
	    if (hash && hash.startsWith('#detail-contact=') && id >= 0) {
	        
	        $('[view-detail]').trigger('click');
	        self.loadModalDetail(id);
	    }

		$(document).on('click', '[view-detail]', function(e) {
			e.preventDefault();

			var id = $(this).attr('view-detail');

			self.loadModalDetail(id);
		});
	},

	loadModalDetail: function(id){
		var self = this;
		
	    if (id <= 0 || self.wrapModalDetail.length == 0) return;
	    
	    KTApp.blockPage(blockOptions);

	    nhMain.callAjax({
	        url: adminPath + '/contact/detail/' + id,
	        dataType: _HTML
	    }).done(function(response) {
	    	
	        KTApp.unblockPage();
	        self.wrapModalDetail.find('.modal-body').html(response);
	        $('[contact-status="'+id+'"]').removeClass('kt-badge--dark').addClass('kt-badge--success').text(nhMain.getLabel('da_doc'));
	        self.wrapModalDetail.modal('show'); 
	    });
	}
}

var responsiveTab = {
	wrapElement: $('[responsive-tab]'),
	init: function(){
		var self = this;

		if(self.wrapElement.length == 0) return false;

		self.responsive();
	},
	responsive: function(){
		var self = this;

		var tabs = self.wrapElement.find('li');	
        var firstTab = tabs.first();

        var heightAllTab = self.wrapElement.outerHeight();
        var heightItemTab = tabs.outerHeight();

        if(heightAllTab > heightItemTab) {

        	var firstTabPos = firstTab.offset();
        	var thisTabPos;

        	tabs.each(function() {
        		var thisTab = $(this);
        		thisTabPos = thisTab.offset();

        		if(thisTabPos.top > firstTabPos.top) {
        			var dropdown = self.wrapElement.find('.view-more');

        			if(dropdown.length == 0) {
                        var dropdownMarkup = '<li class="d-inline-block view-more kt-portlet__head-toolbar">'
                        + '<a href="javascript://" class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa fa-bars m-0"></i></a>'
                        + '<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right px-3 pb-2 pt-3">'
                        + '</div>';
                        dropdown = $(dropdownMarkup);
                        self.wrapElement.append(dropdown);
                                
                    }

                    var previousTab = thisTab.prev();
                    var followingTabs = thisTab.nextAll().not('.view-more');
                    var destination = $('.dropdown-menu', dropdown);
                            
                    if(!thisTab.hasClass('dropdown')) {
                        self.storeTabs(followingTabs, destination);
                        self.storeTabs(thisTab, destination);
                    }
                    self.storeTabs(previousTab, destination);
                            
                    return;
        		}
        	});
        }
	},
	storeTabs: function(tabs, destination) {
        // measure width
        tabs.each(function() {
            var width = $(this).outerWidth(true);
            $(this).data('width', width);          
        });
        tabs.prependTo(destination);
        $('div.dropdown-menu > li').addClass('mr-1 mb-1');
        $('div.dropdown-menu > li:last-child').removeClass('mr-1');
    }

}

jQuery(document).ready(function() {
	viewModalDetail.init();
	responsiveTab.init();
	nhListContact.listData();
});

