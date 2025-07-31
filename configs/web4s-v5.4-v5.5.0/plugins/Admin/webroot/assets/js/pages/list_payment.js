"use strict";

var listGateway = [];
var nhListPayment = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/payment/list/json',
					headers: {
						'X-CSRF-Token': csrfToken
					},
					map: function(raw) {
						var dataSet = raw;
						if (typeof raw.data !== _UNDEFINED) {
							dataSet = raw.data;
						}

						if(typeof(raw.extend) != _UNDEFINED && typeof(raw.extend.list_gateway) != _UNDEFINED){
							listGateway = raw.extend.list_gateway;
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
				class: 'w-kt-table w-md-3 w-xs-10',
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_giao_dich'),
				sortable: false,
				width: 220,
				autoHide: false,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var detailUrl = adminPath + '/payment/detail/' + code;

					return '\
					<div class="code-time nh-weight">\
						<div class="mb-5"> <i class="fas fa-qrcode"></i> <a href="javascript:;" quick-view="'+ code +'">' + code +'</a></div>\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateTimeString(row.created) +'</div>\
					</div>';
				}
			},

			{
				field: 'full_name',
				title: nhMain.getLabel('nguoi_giao_dich'),
				sortable: false,
				template: function(row){
					var full_name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';

					if(typeof(row.object_type) != _UNDEFINED && row.object_type != null && row.object_type == 'customer') {
						var customerURL = adminPath + '/customer/detail/' + row.object_id;
						full_name = '<a target="_blank" href="' + customerURL + '">' + full_name +'</a>';
					}

					return full_name;
				}
			},

			{
				field: 'payment_method',
				title: nhMain.getLabel('phuong_thuc_thanh_toan'),
				sortable: false,
				width: 300,
				template: function(row){
					var payments = {
						'cash': nhMain.getLabel('tien_mat'),
						'bank': nhMain.getLabel('chuyen_khoan'),
						'credit': nhMain.getLabel('quet_the'),
						'cod': nhMain.getLabel('cod')
					};

					var gatewayCode = typeof(row.payment_gateway_code) != _UNDEFINED && row.payment_gateway_code != null ? row.payment_gateway_code : '';
					var paymentMethod = typeof(row.payment_method) != _UNDEFINED && row.payment_method != null ? row.payment_method : '';
					var paymentName = typeof(payments[paymentMethod]) != _UNDEFINED ? payments[paymentMethod] : '';

					var htmlResult = `<span>${paymentName}</span>`;

					if(paymentMethod == 'bank' && gatewayCode.length > 0){
						var gatewayName = typeof(listGateway[gatewayCode]) != _UNDEFINED ? listGateway[gatewayCode] : '';
						htmlResult = `
									<span>
										${nhMain.getLabel('cong_thanh_toan')}:
										<b>
											${gatewayName}
										</b>
									</span>`;
					}
					
					return htmlResult;
				}
			},

			{
				field: 'amount',
				title: nhMain.getLabel('so_tien'),
				width: 100,
				template: function(row) {
					return nhMain.utilities.parseNumberToTextMoney(nhMain.utilities.parseFloat(row.amount));
				},
			},

			{
				field: 'note',
				title: nhMain.getLabel('ghi_chu'),
				sortable: false,
				template: function(row){
					var note = typeof(row.note) != _UNDEFINED && row.note != null ? row.note : '';

					return '\
					<div class="nh-note-customer">' + nhList.template.changeNote(row.id, 'note', note, nhMain.getLabel('ghi_chu')) + '</div>\
					';
				}
			},

			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				sortable: false,
				width: 120,
				template: function(row) {
					var status = '';
					var statusOptions = {
						0: {'title': nhMain.getLabel('da_huy'), 'class': 'kt-badge--dark kt-font-bold'},
						1: {'title': nhMain.getLabel('thanh_cong'), 'class': 'kt-badge--success kt-font-bold'},
						2: {'title': nhMain.getLabel('cho_xet_duyet'), 'class': 'kt-badge--danger kt-font-bold'}
					};
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = '<span class="kt-badge ' + statusOptions[row.status].class + ' kt-badge--inline kt-badge--pill">' + statusOptions[row.status].title + '</span>';
					}
					return status;
				}
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#payment_method').on('change', function() {
		      	datatable.search($(this).val(), 'payment_method');
		    });

			$('#price_from').on('change', function() {
		      	datatable.search($(this).val(), 'price_from');
		    });

		    $('#price_to').on('change', function() {
		      	datatable.search($(this).val(), 'price_to');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $('#payment_from').on('change', function() {
		      	datatable.search($(this).val(), 'payment_from');
		    });

		    $('#payment_to').on('change', function() {
		      	datatable.search($(this).val(), 'payment_to');
		    });

		    $('#note').on('change', function() {
		      	datatable.search($(this).val(), 'note');
		    }); 

		    $('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			// event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	note: adminPath + '/payment/change-note',
			    	status: adminPath + '/payment/change-status',
			    }
		    });

		    $(document).on('click', '[nh-export]', function(e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var page = typeof(datatable.getCurrentPage()) != _UNDEFINED ? datatable.getCurrentPage() : 1;

                var data_filter = {
					lang: nhMain.lang,
					keyword: $('#nh-keyword').val(),
					status: $('[name=status]').val(),
					payment_method: $('[name=payment_method]').val(),
					price_from: $('[name=price_from]').val(),
					price_to: $('[name=price_to]').val(),
					create_from: $('[name=create_from]').val(),
					create_to: $('[name=create_to]').val()
				}

                nhMain.callAjax({
                    url: adminPath + '/payment/list/json',
					data: {
						'data_filter': data_filter,
						'pagination': {page: page},
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

			$('.kt-selectpicker').selectpicker();
			$('.kt_datepicker').datepicker({
				format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
			});

			$(document).on('mouseenter', '.kt-datatable .popover-payment-method', function() {
				$(this).popover({
	    			html: true,
	    			trigger: 'hover',
	    			placement: 'top'
		        });
		        $(this).popover('show');
		    });

		    $(document).on('click', '[quick-view]', function(e) {
				e.preventDefault();

				var modalQuickView = $('#modal-quick-view');
				if(modalQuickView.length == 0) return false;

				modalQuickView.find('.modal-dialog').removeClass('modal-md').addClass('modal-xl');

				var code = $(this).attr('quick-view');

				nhMain.callAjax({
					url: adminPath + '/payment/detail/' + code,
					dataType: _HTML
				}).done(function(response) {
					KTApp.unblockPage();
					modalQuickView.modal('show');
					modalQuickView.find('.modal-body').html(response);

					$('.number-input').each(function() {
						nhMain.input.inputMask.init($(this), 'number');
					});

					$('.kt-selectpicker').selectpicker();
				});
			});

			$(document).on('click', '#confirm-payment', function(e) {
				e.preventDefault();

				var modalQuickView = $('#modal-quick-view');
				if(modalQuickView.length == 0) return false;

				modalQuickView.find('.modal-dialog').removeClass('modal-xl').addClass('modal-md');

				var modalInfoPayment = modalQuickView.find('[wrap-info]');
				var modalConfirmPayment = modalQuickView.find('[wrap-confirm]');
				var modalLogPayment = modalQuickView.find('[wrap-logs]');

				if (modalInfoPayment.length == 0 || modalConfirmPayment.length == 0) return false;

				modalInfoPayment.addClass('d-none');
				modalLogPayment.addClass('d-none');
				modalConfirmPayment.removeClass('d-none');
			});

			$(document).on('click', '#info-payment', function(e) {
				e.preventDefault();

				var modalQuickView = $('#modal-quick-view');
				if(modalQuickView.length == 0) return false;

				modalQuickView.find('.modal-dialog').removeClass('modal-md').addClass('modal-xl');

				var modalInfoPayment = modalQuickView.find('[wrap-info]');
				var modalConfirmPayment = modalQuickView.find('[wrap-confirm]');
				var modalLogPayment = modalQuickView.find('[wrap-logs]');

				if (modalInfoPayment.length == 0 || modalConfirmPayment.length == 0) return false;

				modalInfoPayment.removeClass('d-none');
				modalConfirmPayment.addClass('d-none');
				modalLogPayment.addClass('d-none');
			});

			$(document).on('click', '#logs-payment', function(e) {
				e.preventDefault();

				var modalQuickView = $('#modal-quick-view');
				if(modalQuickView.length == 0) return false;

				modalQuickView.find('.modal-dialog').removeClass('modal-md').addClass('modal-xl');

				var modalInfoPayment = modalQuickView.find('[wrap-info]');
				var modalConfirmPayment = modalQuickView.find('[wrap-confirm]');
				var modalLogPayment = modalQuickView.find('[wrap-logs]');

				if (modalInfoPayment.length == 0 || modalConfirmPayment.length == 0) return false;

				modalInfoPayment.addClass('d-none');
				modalConfirmPayment.addClass('d-none');
				modalLogPayment.removeClass('d-none');
			});

			$(document).on('click', '#btn-confirm-payment', function(e) {
				e.preventDefault();

				var formEl = $('#payment-confirm-form');
				if(formEl.length == 0) return false;

				var validator = formEl.validate({
					ignore: ":hidden",
					rules: {
						amount: {
							required: true
						},
						status: {
							required: true
						}
					},
					messages: {
						amount: {
		                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
		                },
						status: {
		                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
		                }
		            },

		            errorPlacement: function(error, element) {
		            	var messageRequired = element.attr('message-required');
		            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
		            		error.text(messageRequired);
		            	}
		            	error.addClass('invalid-feedback')

		                var group = element.closest('.input-group');
		                if (group.length) {
		                    group.after(error);
		                }else if(element.hasClass('select2-hidden-accessible')){
		            		element.closest('.form-group').append(error);
		                }else{
		                	element.after(error);
		                }
		            },

					invalidHandler: function(event, validator) {
						KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
					}
				});

				if (validator.form()) {
					var btnSave = $(this);

					// show loading
					KTApp.progress(btnSave);
					KTApp.blockPage(blockOptions);
					
					// params call ajax
					var formData = formEl.serialize();
					
					nhMain.callAjax({
						url: formEl.attr('action'),
						data: formData
					}).done(function(response) {
						// hide loading
						KTApp.unprogress(btnSave);
						KTApp.unblockPage();

						//show message and redirect page
					   	var code = response.code || _ERROR;
			        	var message = response.message || '';
			        	var data = response.data || {};

			        	toastr.clear();
			            if (code == _SUCCESS) {
			            	toastr.info(message);

			            	if ($('#modal-quick-view').length != 0) {
			            		$('#modal-quick-view').modal('hide');
			            	}
			            	
			            	$('.kt-datatable').KTDatatable('reload');
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
	nhListPayment.listData();
});

