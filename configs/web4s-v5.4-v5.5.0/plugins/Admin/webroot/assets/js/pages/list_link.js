"use strict";

var nhListLink = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/link/list/json',
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
			type: $('#nh-type').val(),
			language: $('nh-language').val(),
			status: $('#nh-status').val(),
			create_from:$('#create_from').val(),
			create_to:$('#create_to').val()
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
				field: 'url',
				title: nhMain.getLabel('duong_dan'),
				autoHide: false,
				width: 500,
				template: function(row) {
					
					var nameUrl = KTUtil.isset(row, 'url') && row.url != null ? row.url : '';
					var type = KTUtil.isset(row, 'type') && row.type != null ? row.type : '';

					switch(type){
		                case 'category_product':
		                   	var urlDetail = adminPath + '/category/product/update/' + row.foreign_id;		      
		                break;

		                case 'category_article':
		                    var urlDetail = adminPath + '/category/article/update/' + row.foreign_id;		    
		                break;

		                case 'product_detail':
		                    var urlDetail = adminPath + '/product/update/' + row.foreign_id;		                    
		                break;

		                case 'article_detail':
		                    var urlDetail = adminPath + '/article/update/' + row.foreign_id;		                  
		                break;

		                case 'brand_detail':
		                   var urlDetail = adminPath + '/brand/detail/' + row.foreign_id;		                   
		                break;          
            		}
					var viewTemplate = ''
					if(nameUrl.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+nameUrl +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="d-inline kt-user-card-v2__name" target="_blank">'+ nameUrl +'</a>' + viewTemplate + '\
							</div>\
						</div>';
				}
			},	
			{	
				field: 'type',
				title: nhMain.getLabel('loai_duong_dan'),
				autoHide: false,
				width: 200,
				template: function(row) {
					$('[data-field="lang"]').addClass('text-center');
					var type = KTUtil.isset(row, 'type') && row.type != null ? row.type : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					
					switch(type){
		                case 'category_product':		          
		                   	var nameType = nhMain.getLabel('danh_muc_san_pham');
		                   	var urlDetail = adminPath + '/category/product/update/' + row.foreign_id;
		                break;

		                case 'category_article':		                    
		                    var nameType = nhMain.getLabel('danh_muc_bai_viet');
		                    var urlDetail = adminPath + '/category/article/update/' + row.foreign_id;
		                break;

		                case 'product_detail':		                  
		                    var nameType = nhMain.getLabel('san_pham');
		                    var urlDetail = adminPath + '/product/update/' + row.foreign_id;
		                break;

		                case 'article_detail':		                    
		                    var nameType = nhMain.getLabel('bai_viet');
		                    var urlDetail = adminPath + '/article/update/' + row.foreign_id;
		                break;

		                case 'brand_detail':		                   
		                   var nameType = nhMain.getLabel('thuong_hieu');
		                   var urlDetail = adminPath + '/brand/detail/' + row.foreign_id;
		                break;          
            		}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a class="d-inline kt-user-card-v2__name" target="_blank" href="'+ urlDetail +'">'+ nameType +'</a>\
							</div>\
						</div>';
				}
			},			
			{
				field: 'lang',
				title: nhMain.getLabel('ngon_ngu'),				
				width: 150,
				sortable: false,
				template: function(row) {					
					return '\
						<div class="list-flags text-center">\
								<img src="/admin/assets/media/flags/'+row.lang+'.svg" alt="Tiếng Việt" class="flag ">\
						</div>';
				}
			},
			{
				field: 'created',
				title: nhMain.getLabel('ngay_tao'),				
				width: 150,
				sortable: false,

				template: function(row) {
		
					var created = KTUtil.isset(row, 'created') && row.created != null ? nhMain.utilities.parseIntToDateTimeString(row.created) : '';

					return '\
						<div class="">\
								<span>'
									 + created +
								'</span>\
						</div>';
				}
			},
			{
				field: 'updated',
				title: nhMain.getLabel('ngay_cap_nhat'),				
				width: 150,
				sortable: false,

				template: function(row) {
			
					var updated = KTUtil.isset(row, 'updated') && row.updated != null ? nhMain.utilities.parseIntToDateTimeString(row.updated) : '';
					return '\
						<div class="">\
								<span>'
									 + updated +
								'</span>\
						</div>';
				}
			}
			]
	};

	return {
		listData: function() {

			$(document).on('click', '#btn-refresh-search', function() {
			KTApp.blockPage(blockOptions);
	    	$('input').val('');
	    	$('.kt-selectpicker').val('');
	    	$('.kt-selectpicker').selectpicker('refresh');
			datatable.setDataSourceParam('query','');
	    	$('.kt-datatable').KTDatatable('load');
	    	KTApp.unblockPage();
		});

			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });	
		 
		    $('#nh-type').on('change', function() {
		      	datatable.search($(this).val(), 'type');		      
		    });
		    $('#nh-language').on('change', function() {
		      	datatable.search($(this).val(), 'language');		      
		    });	 

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });			   
		    		
		    $('.kt-selectpicker').selectpicker();    
		}
	};
}();

$(document).ready(function() {
	nhListLink.listData();
});

