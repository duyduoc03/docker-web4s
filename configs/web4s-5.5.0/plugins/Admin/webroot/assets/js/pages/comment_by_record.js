'use strict';

var nhCommentByRecord = {
	typePage: null,
	config:{
		max_number_files: 10, // number file upload
		expires_cookie: 10, // number day cookie expires
	},
	btnFilter: $('[btn-filter]'),
	modalFilter: $('#modal-filter-comment'),
	typeFilter: null,
	init: function(){
		var self = this;

		var tabComment = $('#tab-comment');

		$('[nh-btn="show-comment-tab"]').on('show.bs.tab', function (event) {		
			if(tabComment.length == 0 || tabComment.hasClass('loaded')) return;
			
			tabComment.addClass('loaded');
		  	self.initLib();
		});


		if( 
			window.location.hash == '#comment-record' && 
			tabComment.length > 0 &&
			!tabComment.hasClass('loaded')
		){
			$('[nh-btn="show-comment-tab"]').trigger('click');
		}
	},
	initLib: function(){
		var self = this;

		if(self.btnFilter.length == 0) return false;
		if(self.modalFilter.length == 0) return false;
		if($('[list-comment]').length == 0) return false;
		
		self.typePage = $('[list-comment]').data('type');

		$('.kt_datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true,
		});

		self.eventComment();
		self.comment.init();
		self.rating.init();
	},
	eventComment: function(){
		var self = this;

		$(document).on('click', '[change-status]', function() {
			var btnChange = $(this);

			var type = btnChange.data('type') || '';
			var status = btnChange.attr('change-status') || '';
			if(status == '' || type == '') {
				toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_binh_luan'));
		    	return false;
			}

			var id = '';
			if(type == _COMMENT) id = $(this).closest('[nh-comment-item]').attr('nh-comment-item') || '';
			if(type == _RATING) id = $(this).closest('[nh-rating-item]').attr('nh-rating-item') || '';

			if(id == '') {
				toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_binh_luan'));
		    	return false;
			};

			swal.fire({
		        title: nhMain.getLabel('thay_doi_trang_thai'),
		        text: nhMain.getLabel('ban_chac_chan_muon_thay_doi_trang_thai_binh_luan_nay'),
		        type: 'warning',
		        confirmButtonText: nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){

		    		KTApp.blockPage(blockOptions);
		    		nhMain.callAjax({
						url: adminPath + '/comment/change-status',
						data:{
							ids: [id],
							status: status
						}
					}).done(function(response) {
						KTApp.unblockPage();

						var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

					    if (code == _SUCCESS) {
			            	toastr.info(message);

			            	if(status == 1 && type == _COMMENT) btnChange.closest('[nh-comment-item="'+id+'"]').find('.kt-badge').remove();
							if(status == 1 && type == _RATING) btnChange.closest('[nh-rating-item="'+id+'"]').find('.kt-badge').remove();

							if(status == 0 && type == _COMMENT) btnChange.closest('[nh-comment-item="'+id+'"]').remove();
							if(status == 0 && type == _RATING) btnChange.closest('[nh-rating-item="'+id+'"]').remove();
			            } else {
			            	toastr.error(message);
			            }            
					})
		    	}    	
		    });
			return false;
        });
        
        $(document).on('click', '[nh-delete]', function() {
        	var btnDelete = $(this);
			var type = btnDelete.attr('nh-delete') || '';
			if(type == '') {
				toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_binh_luan'));
		    	return false;
			}

			var id = '';
			if(type == _COMMENT) id = $(this).closest('[nh-comment-item]').attr('nh-comment-item') || '';
			if(type == _RATING) id = $(this).closest('[nh-rating-item]').attr('nh-rating-item') || '';

			if(id == '') {
				toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_binh_luan'));
		    	return false;
			};

			swal.fire({
		        title: nhMain.getLabel('xoa_binh_luan'),
		        text: nhMain.getLabel('ban_chac_chan_muon_xoa_binh_luan_nay'),
		        type: 'warning',
		        confirmButtonText: nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){

		    		KTApp.blockPage(blockOptions);
		    		nhMain.callAjax({
						url: adminPath + '/comment/delete',
						data:{
							ids: [id]
						}
					}).done(function(response) {
						KTApp.unblockPage();

						var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

					    if (code == _SUCCESS) {
			            	toastr.info(message);

			            	if(type == _COMMENT) btnDelete.closest('[nh-comment-item="'+id+'"]').remove();
							if(type == _RATING) btnDelete.closest('[nh-rating-item="'+id+'"]').remove();
			            } else {
			            	toastr.error(message);
			            }            
					})
		    	}    	
		    });
			return false;
        });
        
        self.btnFilter.on('click', function() {
			self.typeFilter = $(this).data('type') || '';

			if(self.typeFilter == '') return false;

			self.modalFilter.find('.filter-rating').addClass('d-none');

			var titleModal = nhMain.getLabel('tim_kiem_binh_luan');
			if(self.typeFilter == _RATING) {
				self.modalFilter.find('.filter-rating').removeClass('d-none');
				titleModal = nhMain.getLabel('tim_kiem_danh_gia');
			}

			self.modalFilter.find('.modal-title').text(titleModal);
        });
        
        self.modalFilter.on('click', '[btn-search]', function() {
        	var params = {
				show_loading: false, 
				foreign_id: self.comment.foreign_id,
				keyword: self.modalFilter.find('[name="keyword"]').val(),
				status: self.modalFilter.find('[name="status"]').val() || null,
				images: self.modalFilter.find('[name="images"]').is(':checked') ? 1 : 0,
				create_from: self.modalFilter.find('[name="create_from"]').val(),
				create_to: self.modalFilter.find('[name="create_to"]').val()
			};

			if(self.typeFilter == _COMMENT) {
				self.comment.listElement.html('');
				self.comment.loadComment(params);
			}

			if(self.typeFilter == _RATING){
				self.rating.listElement.html('');

				var rating = self.modalFilter.find('[name="rating"]').val();
				params['rating'] = rating;

				self.rating.loadCommentRating(params);
			}

			self.modalFilter.modal('hide');
        });
	},
	ajaxAddComment: function(data = {}, callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

	  	KTApp.blockPage(blockOptions);
		nhMain.callAjax({
    		async: true,
			url: adminPath + '/comment/admin-reply',
			data: {
				id: typeof(data.id) != _UNDEFINED ? data.id : null,
				content: typeof(data.content) != _UNDEFINED ? data.content : null,
				images: typeof(data.images) != _UNDEFINED ? data.images : [],
				foreign_id: typeof(data.foreign_id) != _UNDEFINED ? data.foreign_id : null,
				parent_id: typeof(data.parent_id) != _UNDEFINED ? data.parent_id : null,
				url: window.location.pathname,
				type_comment: typeof(data.type_comment) != _UNDEFINED ? data.type_comment : null,
				type: self.typePage,
			},
		}).done(function(response) {
			callback(response);
            KTApp.unblockPage();
		});
	},
	ajaxLoadComment: function(params = {}, options = {}, callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

	    if(typeof(options.show_loading) != _UNDEFINED && Boolean(options.show_loading)){
	    	KTApp.blockPage(blockOptions);
	    }

	    if(typeof(params.url) == _UNDEFINED){
	    	params.url = window.location.pathname;
	    }

		nhMain.callAjax({
    		async: true,
			url: adminPath + '/comment/list/' + self.typePage,
			data: params,
		}).done(function(response) {
			callback(response);
			KTApp.unblockPage();
		});
	},
	ajaxUploadImage: function(formData = {}, callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

	    nhMain.callAjax({
    		async: true,
			url: adminPath + '/comment/upload-file',
			data: formData,
			contentType: false,
			processData: false,
		}).done(function(response) {

			var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

        	if (code == _ERROR){
        		toastr.error(message);
        	}

        	if (code == _SUCCESS && !$.isEmptyObject(data)) {
        		callback(data);
            }
		}).fail(function(jqXHR, textStatus, errorThrown){
	    	toastr.error(message);
		});
	},
	comment: {
		config: {
			number_record: 5
		},
		wrapElement: null,
		listElement: null,
		contentElement: null,
		listReplyElement: null,
		content: null,
		images: [],
		number_images: 0,
		scrollHeightDefault: 0,
		template:{
			item: '\
				<li nh-comment-item="" is-parent="true" class="comment-item kt-notes__item">\
					<div class="post-author kt-notes__media">\
						<h3 class="letter-first kt-notes__user kt-font-boldest"></h3>\
					</div>\
					<div class="comment-content kt-notes__content">\
						<div class="kt-notes__section">\
							<div class="kt-notes__info">\
								<span class="kt-notes__title"></span>\
								<span class="kt-badge kt-badge--inline"></span>\
							</div>\
							<div class="kt-notes__dropdown">\
								<a href="#" class="btn btn-sm btn-icon-md btn-icon" data-toggle="dropdown" aria-expanded="false">\
									<i class="flaticon-more-1 kt-font-brand"></i>\
								</a>\
								<div class="dropdown-menu dropdown-menu-right p-0">\
									<ul class="kt-nav">\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-success" data-type="comment" change-status="1"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('duyet')+'</a>\
										</li>\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-warning" data-type="comment" change-status="0"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('khong_duyet')+'</a>\
										</li>\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-danger" nh-delete="comment"><i class="fas fa-trash-alt fs-14 mr-15"></i>'+nhMain.getLabel('xoa')+'</a>\
										</li>\
									</ul>\
								</div>\
							</div>\
						</div>\
						<div inner-content class=" kt-notes__body">\
						</div>\
					</div>\
					<div class="comment-action fs-12 mb-10 mt-10">\
						<div class="inner-reply kt-font-bolder kt-pointer">\
							<i class="flaticon-reply"></i>\
							<span class="number-reply"></span> ' +
							nhMain.getLabel('tra_loi') +
						'</div>	\
						<div class="post-date ml-2"></div>\
					</div>\
				</li>',
			itemReply: '\
				<li nh-comment-item="" class="reply kt-notes__item">\
					<div class="post-author kt-notes__media">\
						<h3 class="letter-first kt-notes__user kt-font-boldest"></h3>\
					</div>\
					<div class="comment-content kt-notes__content">\
						<div class="kt-notes__section">\
							<div class="kt-notes__info">\
								<span class="kt-notes__title"></span>\
								<span class="kt-badge kt-badge--inline"></span>\
							</div>\
							<div class="kt-notes__dropdown">\
								<a href="#" class="btn btn-sm btn-icon-md btn-icon" data-toggle="dropdown" aria-expanded="false">\
									<i class="flaticon-more-1 kt-font-brand"></i>\
								</a>\
								<div class="dropdown-menu dropdown-menu-right p-0">\
									<ul class="kt-nav">\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-success" data-type="comment" change-status="1"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('duyet')+'</a>\
										</li>\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-warning" data-type="comment" change-status="0"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('khong_duyet')+'</a>\
										</li>\
										<li class="kt-nav__item">\
											<a href="javascript://" class="kt-nav__link text-danger" nh-delete="comment"><i class="fas fa-trash-alt fs-14 mr-15"></i>'+nhMain.getLabel('xoa')+'</a>\
										</li>\
									</ul>\
								</div>\
							</div>\
						</div>\
						<div inner-content class=" kt-notes__body">\
						</div>\
					</div>\
					<div class="comment-action fs-12 mb-10 mt-10">\
						<div class="inner-reply kt-font-bolder kt-pointer">\
							<i class="flaticon-reply"></i>\
							<span class="number-reply"></span> ' +
							nhMain.getLabel('tra_loi') +
						'</div>	\
						<div class="post-date ml-2"></div>\
					</div>\
				</li>',
			wrapReply: '<ul class="list-reply p-0 mt-15"></ul>',				
			inputContent: '\
				<div class="edit-comment">\
					<textarea class="form-control" nh-input-comment placeholder="'+ nhMain.getLabel('tra_loi_binh_luan_khach_hang') +'"></textarea>\
					<div class="box-comment">\
						<label>\
							<i nh-trigger-upload class="la la-camera"></i>\
						</label>\
						<input id="upload-files-comment" type="file" multiple="true" style="display: none;" accept=".jpg, .jpeg, .png" value="" />\
					</div>\
					<ul class="comment-images list-image-album d-none"></ul>\
					<span nh-btn-send-comment class="btn btn-sm btn-brand btn-submit mt-5">' +
						nhMain.getLabel('tra_loi') +
					'</span>\
				</div>',
			moreItem: '<a nh-comment-more="" class="comment-more" href="javascript:;">'+ nhMain.getLabel('xem_them_binh_luan') +'</a>',
			wrapListImageSelect: '<ul class="comment-images list-image-album"></ul>',
			imageSelect: '\
				<li nh-item-comment-image class="loading">\
					<img class="img-comment" src="">\
					<i class="close-image"><i class="fa-solid fa-xmark"></i></i>\
				</li>',
			totalComment: '<span class="text-danger font-italic">\
	                (CĂ³ <span number-comment=""></span> tháº£o luáº­n)\
	            </span>',
		},
		commentId: null,
		foreignId: null,
		type_page: null,
		triggerAdd: false,
		init: function(){
			var self = this;

			// validate element
			self.wrapElement = $('[nh-comment]');
			if(self.wrapElement.length == 0){
				toastr.error(nhMain.getLabel('chuc_nang_binh_luan_thieu_dieu_kien_de_hoat_dong'));
				return false;
			}

			self.listElement = self.wrapElement.find('[nh-list-comment]');
			if(self.listElement.length == 0){
				toastr.error(nhMain.getLabel('chuc_nang_binh_luan_thieu_dieu_kien_de_hoat_dong'));
				return false;
			}

			self.contentElement = self.wrapElement.find('[nh-input-comment]');
			if(self.contentElement.length > 0){
				self.initInputContent(self.contentElement);
			}

			self.event();

			self.foreign_id = self.wrapElement.attr('nh-comment');
			self.loadComment({show_loading: false, foreign_id: self.foreign_id});
			
		},
		initInputContent: function(input = null	){
			var self = this;
			if(typeof(input) == _UNDEFINED || input == null || input.length == 0) return false;

			self.scrollHeightDefault = input[0].scrollHeight;
			input[0].setAttribute('style', 'height:' + self.scrollHeightDefault + 'px; overflow-y:hidden;');
			input.on('input', function () {
		        this.style.height = 'auto';
		        this.style.height = this.scrollHeight + 'px';
		    });
		},
		event: function(){
			var self = this;	

			self.wrapElement.on('click', '[nh-btn-send-comment]', function(e){

				// cap nhat lai commentId khi khong tra loi binh luan nao
				if($(this).closest('[nh-comment-item]').length == 0) self.commentId = null;

				self.foreignId = $(this).closest('[nh-comment]').attr('nh-comment');
				self.contentElement = $(this).closest('.edit-comment').find('[nh-input-comment]');

				if(self.contentElement.val().length > 255) {
					toastr.error(nhMain.getLabel('noi_dung_binh_luan_khong_vuot_qua_25_ky_tu'));
					return false;
		        }

				self.addComment();
			});

			self.wrapElement.on('click', '.inner-reply', function(e){
				var commentItem = $(this).closest('li[nh-comment-item][is-parent]');
				if(commentItem.length == 0) return false;

				self.commentId = $(this).closest('[nh-comment-item]').attr('nh-comment-item');

				var commentId = commentItem.attr('nh-comment-item');

				if(!nhMain.utilities.notEmpty(commentId)) return false;

				var numberReply = $(this).find('.number-reply').text();

				var loadReply = false;
				if(nhMain.utilities.notEmpty(numberReply) && commentItem.find('.list-reply').length == 0) {
					loadReply = true;
					commentItem.append(self.template.wrapReply);					
				}

				if(commentItem.find('.edit-comment').length == 0){
					commentItem.append(self.template.inputContent);

					self.contentElement = commentItem.find('[nh-input-comment]');
					self.initInputContent(self.contentElement);
				}

				self.contentElement = commentItem.find('[nh-input-comment]');
				self.contentElement.focus();

				if(loadReply){
					self.loadComment({foreign_id: self.foreign_id, parent_id: commentId});
				}
				
			});

			self.wrapElement.on('click', '[nh-comment-more]', function(e){
				var commentItem = $(this).closest('li.comment-item');

				var page = $(this).attr('nh-comment-more');
				if(!page > 0) return;

				var commentId = commentItem.length > 0 ? parseInt(commentItem.attr('nh-comment-item')) : null;

				self.loadComment({
					foreign_id: self.foreign_id,
					parent_id: commentId,
					page: page
				});
			});

			self.wrapElement.on('click', '[nh-trigger-upload]', function(e){
				var boxComment = $(this).closest('.box-comment');
				if(boxComment.length == 0) return;

				boxComment.find('input#upload-files-comment').trigger('click');
			});

			self.wrapElement.on('change', '#upload-files-comment', function(e) {
				self.showImagesSelect(this);
			});

			self.wrapElement.on('click', '[nh-btn="clear-image"]', function(e) {
				e.preventDefault();

				var wrapImage = $(this).closest('.comment-images');
				$(this).closest('a.kt-media').remove();
				self.number_images--;

				if(self.number_images == 0) wrapImage.addClass('d-none');
			});
		},
		addComment: function(){
			var self = this;

			self.content = $.trim(self.contentElement.val());
			self.images = [];
			self.number_images = 0;

			if(self.content.length == 0){
				toastr.error(nhMain.getLabel('vui_long_nhap_noi_dung_binh_luan'));
				self.contentElement.focus();
				return false;
			}
			
			var itemComment = self.contentElement.closest('li[nh-comment-item]');
			var parent_id = itemComment.length > 0 ? itemComment.attr('nh-comment-item') : null;
			
			var wrapInput = self.contentElement.closest('.edit-comment');
			wrapInput.find('a.kt-media').each(function(index) {
				if($(this).hasClass('kt-spinner')){
					toastr.error('Vui lĂ²ng chá» há»‡ thá»‘ng Ä‘Äƒng táº£i hĂ¬nh áº£nh liĂªn quan');
					return false;
				}

				var imageElement = $(this).find('img[nh-image="selected"]');
				var urlImage = imageElement.attr('src') || '';
				if(urlImage == '') return;
				self.images.push(urlImage);
			});

			var data = {
				id: self.commentId,
				content: self.content,
				foreign_id: self.foreignId,
				parent_id: parent_id,
				type: self.typePage,
				type_comment: _COMMENT,
				rating: self.rating,
				images: JSON.stringify(self.images)
			}
			
  	    	nhCommentByRecord.ajaxAddComment(data, function(response){
  	    		var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	if (code == _ERROR){
	        		var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        		toastr.info(message);
	        	}

	        	if (code == _SUCCESS) {
					var wrapList = null;
					if(itemComment.length > 0){
						var numberReply = itemComment.find('.number-reply:first').text();
						if($.isNumeric(numberReply)){
							numberReply ++;
						}else{
							numberReply = 1;
						}
						itemComment.find('.number-reply:first').text(numberReply);

						if(itemComment.find('.list-reply').length == 0){
							itemComment.find('.edit-comment').before(self.template.wrapReply);
						}							
						wrapList = itemComment.find('.list-reply');
					}

					var data = typeof(response.data) != _UNDEFINED ? response.data : '';
					self.appendComment(data, wrapList, {load_first: parent_id > 0 ? false : true, load_more: false});
					
					self.clearBoxComment();
	            }
	            
  	    	});
		},
		loadComment: function(params = {}){
			var self = this;

  	    	var parent_id = typeof(params.parent_id) != _UNDEFINED && params.parent_id > 0 ? parseInt(params.parent_id) : null;
  	    	var foreign_id = typeof(params.foreign_id) != _UNDEFINED && params.foreign_id > 0 ? parseInt(params.foreign_id) : null;
  	    	var keyword = typeof(params.keyword) != _UNDEFINED && params.keyword != null ? params.keyword : null;
  	    	var status = typeof(params.status) != _UNDEFINED && params.status != null ? parseInt(params.status) : null;
  	    	var images = typeof(params.images) != _UNDEFINED && params.images > 0 ? parseInt(params.images) : null;
  	    	var create_from = typeof(params.create_from) != _UNDEFINED && params.create_from != null ? params.create_from : null;
  	    	var create_to = typeof(params.create_to) != _UNDEFINED && params.create_to != null ? params.create_to : null;

			nhCommentByRecord.ajaxLoadComment(
  	    	{
  	    		data_filter: {
                	type: self.typePage,
	  	    		foreign_id: foreign_id,
	  	    		parent_id: parent_id,
	  	    		type_comment: _COMMENT,
	  	    		keyword: keyword,
                	status: status,
                	images: images,
                	create_from: create_from,
                	create_to: create_to
                },
                pagination: {
                    page: typeof(params.page) != _UNDEFINED ? params.page : 1,
                    perpage: typeof(self.config.number_record) != _UNDEFINED ? self.config.number_record : 5
                },
                sort: {
                	sort_field: typeof(self.config.sort_field) != _UNDEFINED ? self.config.sort_field : null,
	  	    		sort_type: typeof(self.config.sort_type) != _UNDEFINED ? self.config.sort_type : null
                }
  	    	}, 
  	    	{
  	    		show_loading: typeof(params.show_loading) != _UNDEFINED ? Boolean(params.show_loading) : true
  	    	},
  	    	function(response){
  	    		var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var comments = typeof(response.data) != _UNDEFINED ? response.data : {};

	        	if (code == _ERROR){
	        		var message =  typeof(response.message) != _UNDEFINED ? response.message : null;
	        		toastr.error(message);
	        	}

	        	self.wrapElement = $('[nh-comment="'+foreign_id+'"]');

	        	if (code == _SUCCESS && !nhMain.utilities.notEmpty(comments)) self.listElement.text(nhMain.getLabel('khong_co_binh_luan'));

	        	if (code == _SUCCESS && nhMain.utilities.notEmpty(comments)) {
	        		var wrapReplyElement = null;
					if(parent_id > 0){
						wrapReplyElement = self.wrapElement.find('li[nh-comment-item="' + parent_id + '"]').find('.list-reply');
					}

					$.each(comments, function( index, comment) {						
						self.appendComment(comment, wrapReplyElement, {load_more: true, parent_id: parent_id});
					});

					var pagination = typeof(response['meta']) != _UNDEFINED ? response['meta'] : [];
					var total = typeof(pagination.total) ? parseInt(pagination.total) : 0;
					var page = typeof(pagination.page) ? parseInt(pagination.page) : 0;
					var pages = typeof(pagination.pages) ? parseInt(pagination.pages) : 0;	

					if(page < pages){
						if(parent_id > 0){
							var replyCommentElement = self.wrapElement.find('li[nh-comment-item="' + parent_id + '"]');
							if(replyCommentElement.length > 0 && replyCommentElement.find('[nh-comment-more]').length == 0){
								replyCommentElement.find('.list-reply').after(self.template.moreItem);
							}							
							replyCommentElement.find('[nh-comment-more]').attr('nh-comment-more', page + 1);
						}else{
							if(self.wrapElement.find('> [nh-comment-more]').length == 0){
								self.wrapElement.find('[nh-list-comment]').after(self.template.moreItem);
							}
							self.wrapElement.find('> [nh-comment-more]').attr('nh-comment-more', page + 1);
						}				
					} else if(isNaN(parent_id) || parent_id == null || parent_id == _UNDEFINED) {
                        self.wrapElement.find('> [nh-comment-more]').remove();
                    } else{
                  		var replyCommentElement = self.wrapElement.find('li[nh-comment-item="' + parent_id + '"]');
                  		replyCommentElement.find('[nh-comment-more]').remove();
                    }

                    if(total > 0 && page == 1 && parent_id == null) {
                    	var totalElement = self.wrapElement.siblings('.kt-notes__content').append(self.template.totalComment);
                    	totalElement.find('[number-comment]').text(nhMain.utilities.parseNumberToTextMoney(total));
                	}

	            }else{
	            	self.wrapElement.find('> [nh-comment-more]').addClass('d-none');
	            }
	            
  	    	});
		},
		appendComment: function(comment = {}, wrapReplyElement = null, params = {}){
			var self = this;
				
			var loadFirst = typeof(params.load_first) != _UNDEFINED ? Boolean(params.load_first) : false;
			var loadMore = typeof(params.load_more) != _UNDEFINED ? Boolean(params.load_more) : false;
            var parentId = typeof(params.parent_id) != _UNDEFINED ? parseInt(params.parent_id) : null;

			var wrapElement = null;
			var htmlItem = null;
			var appendItem = null;

			var foreignId = typeof(comment.foreign_id) != _UNDEFINED ? parseInt(comment.foreign_id) : null;
			if(!nhMain.utilities.notEmpty(wrapReplyElement)){
				wrapElement = $('[nh-list-comment]');
				htmlItem = self.template.item;
			}else{
				wrapElement = wrapReplyElement;
				htmlItem = self.template.itemReply;
			}

			if(loadFirst){
				wrapElement.prepend(htmlItem);
				appendItem = wrapElement.find('> li[nh-comment-item]:first-child');
			}else{

				if(loadMore && parentId > 0) {
                    wrapElement.append(htmlItem);
                    appendItem = wrapElement.find('[nh-comment-item]:last-child');
                }else if(loadMore && foreignId){
                    wrapElement.closest('[nh-comment="'+foreignId+'"]').find('[nh-list-comment]');
                    wrapElement.append(htmlItem);
                    appendItem = wrapElement.find('[nh-comment-item]:last-child');
                }else{
                    wrapElement.prepend(htmlItem);
                    appendItem = wrapElement.find('> [nh-comment-item]:first-child');
                }
			}
			if(appendItem.length == 0) return;
			
			var commentId = typeof(comment.id) != _UNDEFINED ? parseInt(comment.id) : null;
			var fullName = typeof(comment.full_name) != _UNDEFINED ? comment.full_name : '';
			var content = typeof(comment.content) != _UNDEFINED ? comment.content : '';
			var time = typeof(comment.time) != _UNDEFINED ? comment.time : '';
			var fullTime = typeof(comment.full_time) != _UNDEFINED ? comment.full_time : '';
			var numberReply = typeof(comment.number_reply) != _UNDEFINED ? comment.number_reply : null;
			var numberLike = typeof(comment.number_like) != _UNDEFINED ? comment.number_like : null;
			var images = typeof(comment.images) != _UNDEFINED ? comment.images : [];
			var isAdmin = typeof(comment.is_admin) != _UNDEFINED ? parseInt(comment.is_admin) : 0;
			var status = typeof(comment.status) != _UNDEFINED ? parseInt(comment.status) : 0;


			if(!nhMain.utilities.notEmpty(fullName) || !nhMain.utilities.notEmpty(content) || !nhMain.utilities.notEmpty(foreignId)) return;

			var letterFirst = fullName.slice(0, 1);

			appendItem.attr('nh-comment-item', commentId);
			appendItem.find('.kt-notes__content .kt-notes__title').text(fullName);
			appendItem.find('.post-author .letter-first').text(letterFirst);			
			appendItem.find('.comment-content [inner-content]').html(content.replace(/\n/g, '<br />'));
			appendItem.find('.comment-action .post-date').text(time);
			appendItem.find('.comment-action .number-reply').text(numberReply);

			if(isAdmin){				
				appendItem.find('.kt-notes__title').append('<span class="is-admin btn btn-sm btn-info kt-font-bolder py-0 px-1 ml-1">'+ nhMain.getLabel('quan_tri_vien') +'</span>');
			}

			if(status == 2 && !isAdmin){				
				appendItem.find('.kt-notes__content .kt-badge').addClass('kt-badge--warning').text(nhMain.getLabel('cho_duyet'));
			}

			if(images.length > 0){
				appendItem.find('.comment-content [inner-content]').after('<div class="album-images list-image-album mb-2 symbol-group symbol-hover"></div>');
				var wrapAlbum = appendItem.find('.album-images');
				$.each(images, function(index, image) {
					var thumb = nhMain.utilities.getThumbs(image, 150);
					wrapAlbum.append('<a href="' + cdnUrl + image + '" target="_blank" data-lightbox="'+commentId+'" class="symbol kt-media kt-media--lg mr-10 position-relative"><img src="' + cdnUrl + thumb + '" ></a>');
				});
			}
		},
		showImagesSelect: function(input = null){
			var self = this;
			if(input == null || typeof(input.files) == _UNDEFINED){
				return false;
			}			

			var wrapComment = $(input).closest('.edit-comment');
			var boxComment = wrapComment.find('.box-comment');
			if(wrapComment.length == 0 || boxComment.length == 0){
				return false;
			}

			var wrapAlbum = wrapComment.find('.comment-images');
			if(wrapAlbum.length == 0){
				boxComment.after(self.template.wrapListImageSelect);
				wrapAlbum = wrapComment.find('.comment-images');
			}

			let number_file_upload = 0;
			$.each(input.files, function(index, file) {
				if(number_file_upload >= nhCommentByRecord.config.max_number_files) return;

				if(number_file_upload > 0) index = number_file_upload++;
				if(number_file_upload == 0) number_file_upload++;

				var fileReader = new FileReader();
				fileReader.readAsDataURL(file);
				fileReader.onload = function(e) {
			        self.appendImageSelect(fileReader.result, input);
			    }				
			});
			
			$.each(input.files, function(index, file) {
				if(self.number_images >= nhCommentByRecord.config.max_number_files) return;

				if(self.number_images > 0) index = self.number_images++;
				if(self.number_images == 0) self.number_images++;

				var formData = new FormData();
				formData.append('file', file);
				formData.append('path', _COMMENT);

				nhCommentByRecord.ajaxUploadImage(formData, function(data){
					var urlImage = data.url || '';
	        		var wrapImage = wrapAlbum.find('a:eq('+ index +')');

					if(wrapImage.length > 0){
						wrapImage.attr('href', cdnUrl + urlImage).attr('target', '_blank').removeClass('kt-spinner kt-spinner--sm kt-spinner--brand');
						wrapImage.find('img[nh-image="selected"]').attr('src', cdnUrl + urlImage);
					}
				});
			});
		},
		appendImageSelect: function(base64Url = null, input = null, params = {}){
			var self = this;

			if(base64Url == null || typeof(base64Url) == _UNDEFINED || base64Url.length == 0) return;

			var wrapAlbum = $(input).closest('.edit-comment').find('.comment-images');
			if(wrapAlbum.length == 0){
				return false;
			}

			wrapAlbum.removeClass('d-none');

			var htmlItem = `
				<a href="javascript:;" class="kt-media kt-media--lg mr-10 position-relative kt-spinner kt-spinner--sm kt-spinner--brand">
                    <img nh-image="selected" src="${base64Url}">
                    <span nh-btn="clear-image" class="btn-clear-image-album" title="XĂ³a">
                        <i class="fa fa-times"></i>
                    </span>
                </a>`;

            wrapAlbum.append(htmlItem);

            var uploaded = params.uploaded || 0;
			if(uploaded > 0) wrapElement.find('li:last-child').removeClass('loading');
		},
		clearBoxComment: function(){
			var self = this;

			var wrap = self.contentElement.closest('.edit-comment');
			if(wrap.length == 0){
				return false;
			}

			self.contentElement.val('');
			self.contentElement.css('height', self.scrollHeightDefault + 'px')
			
			wrap.find('.comment-images').remove();
		}
	},
	rating: {
		config: {},
		content: null,
		rating: null,
		triggerAdd: false,
		wrapElement: null,
		listElement: null,
		contentElement: null,
		ratingItem: null,
		foreignId: null,
		ratingId: null,
		images: [],
		number_images: 0,
		scrollHeightDefault: 0,
		template:{
			item: '\
				<li nh-rating-item="" is-parent="true" class="item">\
					<div class="box-rating">\
						<div class="author-info">\
							<span class="letter-first"></span>\
							<div>\
								<span class="post-author"></span>\
								<br />\
								<span class="post-date"></span>\
							</div>\
						</div>\
						<div class="rating-content">\
			                <div class="vote-rating">\
				                <div class="d-flex">\
					                <div class="star-rating">\
					                    <span style="width:100%"></span>\
					                </div>\
				                    <span class="kt-badge kt-badge--inline ml-5"></span>\
				                </div>\
				                <div class="kt-notes__dropdown">\
									<a href="#" class="btn btn-sm btn-icon-md btn-icon h-auto" data-toggle="dropdown" aria-expanded="false">\
										<i class="flaticon-more-1 kt-font-brand"></i>\
									</a>\
									<div class="dropdown-menu dropdown-menu-right p-0">\
										<ul class="kt-nav">\
											<li class="kt-nav__item">\
												<a href="javascript://" class="kt-nav__link text-success" data-type="rating" change-status="1"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('duyet')+'</a>\
											</li>\
											<li class="kt-nav__item">\
												<a href="javascript://" class="kt-nav__link text-warning" data-type="rating" change-status="0"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('khong_duyet')+'</a>\
											</li>\
											<li class="kt-nav__item">\
												<a href="javascript://" class="kt-nav__link text-danger" nh-delete="rating"><i class="fas fa-trash-alt fs-14 mr-15"></i>'+nhMain.getLabel('xoa')+'</a>\
											</li>\
										</ul>\
									</div>\
								</div>\
			                </div>\
							<div class="description"></div>\
							<div class="btn-action">\
								<div class="inner-reply">\
									<i class="flaticon-reply"></i>\
									<span class="number-reply"></span> '+ nhMain.getLabel('tra_loi') +'\
								</div>	\
							</div>\
						</div>\
					</div>\
				</li>',
			itemReply: '\
				<li nh-rating-item="" class="item">\
					<div class="author-info">\
						<div class="meta">\
							<span class="post-author"></span> - \
							<span class="post-date"></span>\
							<span class="kt-badge kt-badge--inline ml-5"></span>\
						</div>\
						<div class="kt-notes__dropdown">\
							<a href="#" class="btn btn-sm btn-icon-md btn-icon h-auto" data-toggle="dropdown" aria-expanded="false">\
								<i class="flaticon-more-1 kt-font-brand"></i>\
							</a>\
							<div class="dropdown-menu dropdown-menu-right p-0">\
								<ul class="kt-nav">\
									<li class="kt-nav__item">\
										<a href="javascript://" class="kt-nav__link text-success" data-type="rating" change-status="1"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('duyet')+'</a>\
									</li>\
									<li class="kt-nav__item">\
										<a href="javascript://" class="kt-nav__link text-warning" data-type="rating" change-status="0"><i class="fas fa-check-circle fs-14 mr-5"></i>'+nhMain.getLabel('khong_duyet')+'</a>\
									</li>\
									<li class="kt-nav__item">\
										<a href="javascript://" class="kt-nav__link text-danger" nh-delete="rating"><i class="fas fa-trash-alt fs-14 mr-15"></i>'+nhMain.getLabel('xoa')+'</a>\
									</li>\
								</ul>\
							</div>\
						</div>\
					</div>\
					<div class="rating-content">\
						<div class="description"></div>\
					</div>\
				</li>',
			moreItem: '<a nh-comment-more="" class="comment-more" href="javascript:;">'+ nhMain.getLabel('xem_them_danh_gia') +'</a>',
			inputReplyRating: '\
				<div class="edit-comment">\
					<textarea class="form-control" nh-input-rating placeholder="'+ nhMain.getLabel('tra_loi_binh_luan_khach_hang') +'"></textarea>\
					<div class="box-comment">\
						<label>\
							<i nh-input-rating-images class="la la-camera"></i>\
						</label>\
						<input id="upload-files-comment" type="file" multiple="true" style="display: none;" accept=".jpg, .jpeg, .png" value="" />\
					</div>\
					<ul class="comment-images list-image-album d-none"></ul>\
					<span nh-btn-reply-rating class="btn btn-sm btn-brand btn-submit mt-5">' +
						nhMain.getLabel('tra_loi') +
					'</span>\
				</div>',
			wrapReply: '<ul class="child-reply"></ul>',	
			wrapListImageSelect: '<ul class="comment-images list-image-album"></ul>',
			imageSelect: '\
				<li nh-item-rating-image class="loading">\
					<img class="img-comment" src="">\
					<i class="close-image"><i class="fa-solid fa-xmark"></i></i>\
				</li>'
		},		
		init: function(){
			var self = this;

			self.wrapElement = $('[nh-rating]');
			self.listElement = self.wrapElement.find('[nh-list-rating]');

			if(self.wrapElement.length == 0) return false;

			if(self.listElement.length == 0){
				toastr.error(nhMain.getLabel('chuc_nang_danh_gia_thieu_dieu_kien_de_hoat_dong'));
				return false;
			}

			self.event();			

			self.foreignId = self.wrapElement.attr('nh-rating');
			self.loadCommentRating({show_loading: false, foreign_id: self.foreignId});
		},
		initInputContent: function(input = null	){
			var self = this;
			if(typeof(input) == _UNDEFINED || input == null || input.length == 0) return false;

			self.scrollHeightDefault = input[0].scrollHeight;
			input[0].setAttribute('style', 'height:' + self.scrollHeightDefault + 'px; overflow-y:hidden;');
			input.on('input', function () {
		        this.style.height = 'auto';
		        this.style.height = this.scrollHeight + 'px';
		    });
		},
		event: function(){
			var self = this;

			self.wrapElement.on('click', '[nh-btn-reply-rating]', function(e){
				e.preventDefault();

				self.ratingItem = $(this).closest('li[nh-rating-item]');
				if(self.ratingItem.length == 0) return false;

				self.contentElement = self.ratingItem.find('textarea[nh-input-rating]');
				if(self.contentElement.length == 0) return false;

				self.addCommentRating();
			});

			self.wrapElement.on('click', '.inner-reply', function(e){
				self.ratingItem = $(this).closest('li[nh-rating-item][is-parent]');
				if(self.ratingItem.length == 0) return false;

				self.ratingId = self.ratingItem.attr('nh-rating-item');
				if(!nhMain.utilities.notEmpty(self.ratingId)) return false;

				if(self.ratingItem.find('.entry-reply').length == 0){
					self.ratingItem.find('.rating-content').append('<div class="entry-reply"></div>');
				}

				var replyWrap = self.ratingItem.find('.entry-reply');
				var numberReply = $(this).find('.number-reply').text();
				var loadReply = false;
				if(nhMain.utilities.notEmpty(numberReply) && replyWrap.find('.child-reply').length == 0) {
					loadReply = true;
					replyWrap.prepend(self.template.wrapReply);
				}

				if(self.ratingItem.find('.edit-comment').length == 0){
					replyWrap.append(self.template.inputReplyRating);

					self.contentElement = replyWrap.find('[nh-input-rating]');
					self.initInputContent(self.contentElement);
				}

				self.contentElement = self.ratingItem.find('[nh-input-rating]');
				self.contentElement.focus();

				if(loadReply){
					self.loadCommentRating({foreign_id: self.foreignId, parent_id: self.ratingId});
				}
			});

			self.wrapElement.on('click', '[nh-comment-more]', function(e){
				e.preventDefault();

				self.ratingItem = $(this).closest('li[nh-rating-item]');

				var page = $(this).attr('nh-comment-more');
				if(!page > 0) return;

				var commentId = self.ratingItem.length > 0 ? self.ratingItem.attr('nh-rating-item') : null;
				self.loadCommentRating({
					foreign_id: self.foreignId,
					parent_id: commentId,
					page: page
				});
			});

			self.wrapElement.on('click', '[nh-input-rating-images]', function(e){
				var boxComment = $(this).closest('.box-comment');
				if(boxComment.length == 0) return;

				boxComment.find('input#upload-files-comment').trigger('click');
			});

			self.wrapElement.on('change', '#upload-files-comment', function(e) {
				self.showImagesRating(this);
			});

			self.wrapElement.on('click', '.comment-images .close-image', function(e){
				$(this).closest('li').remove();
			});
		},
		addCommentRating: function(){
			var self = this;

			self.content = $.trim(self.contentElement.val());
			self.images = [];
			self.number_images = 0;

			if(self.content.length == 0){
				nhMain.showAlert(_ERROR, nhMain.getLabel('vui_long_nhap_noi_dung_danh_gia'));
				self.contentElement.focus();
				return false;
			}

			var parent_id = null;
			if(self.contentElement.closest('li[nh-rating-item]').length > 0){
				var parent_id = self.contentElement.closest('li[nh-rating-item]').attr('nh-rating-item');
			}

			if(parent_id != null){
				self.rating = null;
			}

			var wrapInput = self.contentElement.closest('.edit-comment');
			wrapInput.find('a.kt-media').each(function(index) {
				if($(this).hasClass('kt-spinner')){
					toastr.error('Vui lòng chờ tải ảnh');
					return false;
				}

				var imageElement = $(this).find('img[nh-image="selected"]');
				var urlImage = imageElement.attr('src') || '';
				if(urlImage == '') return;
				self.images.push(urlImage);
			});

			var data = {
				id: self.ratingId,
				content: self.content,
				foreign_id: self.foreignId,
				parent_id: parent_id,
				type_comment: _RATING,
				rating: self.rating,
				images: JSON.stringify(self.images)
			}
			
  	    	nhCommentByRecord.ajaxAddComment(data, function(response){
  	    		var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	if (code == _ERROR){
	        		var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        		toastr.error(_ERROR, message);
	        	}

	        	if (code == _SUCCESS) {
					var wrapList = null;
					if(nhMain.utilities.notEmpty(self.ratingItem)){
						var numberReply = self.ratingItem.find('.number-reply:first').text();
						if($.isNumeric(numberReply)){
							numberReply ++;
						}else{
							numberReply = 1;
						}
						self.ratingItem.find('.number-reply:first').text(numberReply);

						if(self.ratingItem.find('.child-reply').length == 0){
							self.ratingItem.find('.rating-form').before(self.template.wrapReply);
						}
						var wrapList = self.ratingItem.find('.child-reply');
					}
					
					var data = typeof(response.data) != _UNDEFINED ? response.data : '';
					self.appendCommentRating(data, wrapList, {load_first: parent_id > 0 ? false : true, load_more: false});

					if(!parent_id > 0){
						var listRating = nhMain.utilities.notEmpty($.cookie(_RATING_LIST)) ? JSON.parse($.cookie(_RATING_LIST)) : [];
						if($.inArray(window.location.pathname, listRating) == -1){
							listRating.push(window.location.pathname);
							$.cookie(_RATING_LIST, JSON.stringify(listRating), {expires: self.config.expires_cookie});
						}						
					}

					self.clearBoxRating();
	            }
	            
  	    	});
		},
		loadCommentRating: function(params = {}){
			var self = this;

  	    	var parent_id = typeof(params.parent_id) != _UNDEFINED && params.parent_id > 0 ? parseInt(params.parent_id) : null;
  	    	var foreign_id = typeof(params.foreign_id) != _UNDEFINED && params.foreign_id > 0 ? parseInt(params.foreign_id) : null;
  	    	var rating = typeof(params.rating) != _UNDEFINED && params.rating > 0 ? parseInt(params.rating) : null;
  	    	var keyword = typeof(params.keyword) != _UNDEFINED && params.keyword != null ? params.keyword : null;
  	    	var status = typeof(params.status) != _UNDEFINED && params.status != null ? parseInt(params.status) : null;
  	    	var images = typeof(params.images) != _UNDEFINED && params.images > 0 ? parseInt(params.images) : null;
  	    	var create_from = typeof(params.create_from) != _UNDEFINED && params.create_from != null ? params.create_from : null;
  	    	var create_to = typeof(params.create_to) != _UNDEFINED && params.create_to != null ? params.create_to : null;

  	    	nhCommentByRecord.ajaxLoadComment(
  	    	{
  	    		data_filter: {
                	type: self.typePage,
	  	    		foreign_id: foreign_id,
	  	    		parent_id: parent_id,
	  	    		type_comment: _RATING,
	  	    		keyword: keyword,
	  	    		rating: rating,
                	status: status,
                	images: images,
                	create_from: create_from,
                	create_to: create_to
                },
                pagination: {
                    page: typeof(params.page) != _UNDEFINED ? params.page : 1,
                    perpage: typeof(self.config.number_record) != _UNDEFINED ? self.config.number_record : 5
                },
                sort: {
                	sort_field: typeof(self.config.sort_field) != _UNDEFINED ? self.config.sort_field : null,
	  	    		sort_type: typeof(self.config.sort_type) != _UNDEFINED ? self.config.sort_type : null
                }
  	    	}, 
  	    	{
  	    		show_loading: typeof(params.show_loading) != _UNDEFINED ? Boolean(params.show_loading) : true
  	    	},
  	    	function(response){
  	    		var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var comments = typeof(response.data) != _UNDEFINED ? response.data : {};

	        	if (code == _ERROR){
	        		var message =  typeof(response.message) != _UNDEFINED ? response.message : null;
	        		toastr.error(message);
	        	}
	        	
	        	if (code == _SUCCESS && !nhMain.utilities.notEmpty(comments)) self.listElement.text(nhMain.getLabel('khong_co_danh_gia'));

	        	if (code == _SUCCESS && nhMain.utilities.notEmpty(comments)) {
	        		var wrapReplyElement = null;
					if(parent_id > 0){
						wrapReplyElement = self.wrapElement.find('li[nh-rating-item="' + parent_id + '"]').find('.child-reply');
					}

					$.each(comments, function( index, comment) {						
						self.appendCommentRating(comment, wrapReplyElement, {load_more: true, parent_id: parent_id});
					});

					var pagination = typeof(response['meta']) != _UNDEFINED ? response['meta'] : [];
					var page = typeof(pagination.page) ? parseInt(pagination.page) : 0;
					var pages = typeof(pagination.pages) ? parseInt(pagination.pages) : 0;
					if(page < pages){
						if(parent_id > 0){
							var replyRatingElement = self.wrapElement.find('li[nh-rating-item="' + parent_id + '"]');
							if(replyRatingElement.length > 0 && replyRatingElement.find('[nh-comment-more]').length == 0){
								replyRatingElement.find('.child-reply').after(self.template.moreItem);
							}							
							replyRatingElement.find('[nh-comment-more]').attr('nh-comment-more', page + 1);
						}else{
							if(self.wrapElement.find('> [nh-comment-more]').length == 0){
								self.wrapElement.find('[nh-list-rating]').after(self.template.moreItem);
							}
							self.wrapElement.find('> [nh-comment-more]').attr('nh-comment-more', page + 1);
						}						
					}else if(isNaN(parent_id) || parent_id == null || parent_id == _UNDEFINED) {
                        self.wrapElement.find('> [nh-comment-more]').remove();
                    } else{
                  		var replyCommentElement = self.wrapElement.find('li[nh-rating-item="' + parent_id + '"]');
                  		replyCommentElement.find('[nh-comment-more]').remove();
                    }

	            } else {
					self.wrapElement.find('> [nh-comment-more]').remove();
				}

  	    	});
		},
		appendCommentRating: function(comment = {}, wrapReplyElement = null, params = {}){
			var self = this;
				
			var loadFirst = typeof(params.load_first) != _UNDEFINED ? Boolean(params.load_first) : false;
			var loadMore = typeof(params.load_more) != _UNDEFINED ? Boolean(params.load_more) : false;
            var parentId = typeof(params.parent_id) != _UNDEFINED ? parseInt(params.parent_id) : null;

			var wrapElement = null;
			var htmlItem = null;
			var appendItem = null;

			if(!nhMain.utilities.notEmpty(wrapReplyElement)){
				wrapElement = $('[nh-list-rating]');
				htmlItem = self.template.item;
			}else{
				wrapElement = wrapReplyElement;
				htmlItem = self.template.itemReply;
			}

			if(loadFirst){
				wrapElement.prepend(htmlItem);
				appendItem = wrapElement.find('> li[nh-rating-item]:first-child');
			}else{

				if(loadMore && parentId > 0) {
                    wrapElement.append(htmlItem);
                    appendItem = wrapElement.find('[nh-rating-item]:last-child');
                }else if(loadMore && self.foreignId){
                    wrapElement.closest('[nh-rating="'+self.foreignId+'"]').find('[nh-list-rating]');
                    wrapElement.append(htmlItem);
                    appendItem = wrapElement.find('[nh-rating-item]:last-child');
                }else{
                    wrapElement.prepend(htmlItem);
                    appendItem = wrapElement.find('> [nh-rating-item]:first-child');
                }
			}

			if(appendItem.length == 0) return;

			var commentId = typeof(comment.id) != _UNDEFINED ? parseInt(comment.id) : null;
			var fullName = typeof(comment.full_name) != _UNDEFINED ? comment.full_name : '';
			var content = typeof(comment.content) != _UNDEFINED ? comment.content : '';
			var time = typeof(comment.time) != _UNDEFINED ? comment.time : '';
			var fullTime = typeof(comment.full_time) != _UNDEFINED ? comment.full_time : '';
			var numberReply = typeof(comment.number_reply) != _UNDEFINED ? comment.number_reply : null;
			var numberLike = typeof(comment.number_like) != _UNDEFINED ? comment.number_like : null;
			var images = typeof(comment.images) != _UNDEFINED ? comment.images : [];
			var isAdmin = typeof(comment.is_admin) != _UNDEFINED ? parseInt(comment.is_admin) : 0;
			var status = typeof(comment.status) != _UNDEFINED ? parseInt(comment.status) : 0;
			var rating = typeof(comment.rating) != _UNDEFINED ? parseInt(comment.rating) : 0;

			var widthRating = 0;
			if(rating >= 1 && rating <= 5){
				widthRating = rating * 20;
			}

			if(!nhMain.utilities.notEmpty(fullName) || !nhMain.utilities.notEmpty(content)) return;

			var letterFirst = fullName.slice(0, 1);
			appendItem.attr('nh-rating-item', commentId);
			appendItem.find('.author-info .post-author').text(fullName);
			appendItem.find('.author-info .post-date').text(time);
			appendItem.find('.author-info .letter-first').text(letterFirst);
			appendItem.find('.rating-content .description').text(content);
			appendItem.find('.rating-content .number-reply').text(numberReply);
			appendItem.find('.rating-content .number-like').text(numberLike);

			appendItem.find('.star-rating span').css('width', widthRating + '%');

			if(isAdmin){
				appendItem.find('.post-author').append('<span class="is-admin btn btn-sm btn-info kt-font-bolder py-0 px-1 ml-2">'+ nhMain.getLabel('quan_tri_vien') +'</span>');
			}

			if(status == 2){
				appendItem.find('.kt-badge').addClass('kt-badge--warning').text(nhMain.getLabel('cho_duyet'));
				appendItem.find('.child-reply .kt-badge').addClass('kt-badge--warning').text(nhMain.getLabel('cho_duyet'));
			}

			if(images.length > 0){
				appendItem.find('.rating-content .description').after('<div class="album-images list-image-album symbol-group symbol-hover"></div>');
				var wrapAlbum = appendItem.find('.album-images');
				$.each(images, function(index, image) {
					var thumb = nhMain.utilities.getThumbs(image, 150);
					wrapAlbum.append('<a href="' + cdnUrl + image + '" target="_blank" data-lightbox="'+commentId+'" class="symbol kt-media kt-media--lg mr-10 position-relative"><img class="image-comment1" src="' + cdnUrl + thumb + '" ></a>');
				});
			}
		},
		clearBoxRating: function(){
			var self = this;

			var wrap = self.contentElement.closest('.edit-comment');
			if(wrap.length == 0) return false;
			
			self.contentElement.val('');
			wrap.find('.comment-images').remove();
		},
		showImagesRating: function(input = null){
			var self = this;

			if(input == null || typeof(input.files) == _UNDEFINED){
				return false;
			}

			var wrapComment = $(input).closest('.edit-comment');
			var boxComment = wrapComment.find('.box-comment');
			if(wrapComment.length == 0 || boxComment.length == 0){
				return false;
			}

			var wrapAlbum = wrapComment.find('.comment-images');
			if(wrapAlbum.length == 0){
				boxComment.after(self.template.wrapListImageSelect);
				wrapAlbum = wrapComment.find('.comment-images');
			}

			$.each(input.files, function(index, file) {
				if(index >= nhCommentByRecord.config.max_number_files) return;

				var fileReader = new FileReader();
				fileReader.readAsDataURL(file);
				fileReader.onload = function(e) {
			        self.appendImageRating(fileReader.result, input);
			    }
			});

			$.each(input.files, function(index, file) {
				if(index >= nhCommentByRecord.config.max_number_files) return;

				var wrapImage = wrapAlbum.find('li:eq('+ index +')');

				var formData = new FormData();
				formData.append('file', file);
				formData.append('path', _RATING);

				nhCommentByRecord.ajaxUploadImage(formData, function(data){
					var urlImage = typeof(data.url) != _UNDEFINED ? data.url : null;
	        		var wrapImage = wrapAlbum.find('a:eq('+ index +')');

	        		console.log(wrapImage);
					if(wrapImage.length > 0){
						wrapImage.attr('href', cdnUrl + urlImage).attr('target', '_blank').removeClass('kt-spinner kt-spinner--sm kt-spinner--brand');
						wrapImage.find('img[nh-image="selected"]').attr('src', cdnUrl + urlImage);
					}
				});
			});
		},
		appendImageRating: function(urlImage = null, input = null, params = {}){
			var self = this;

			if(!nhMain.utilities.notEmpty(urlImage)) return false;

			var wrapAlbum = $(input).closest('.edit-comment').find('.comment-images');
			if(wrapAlbum.length == 0) return false;

			wrapAlbum.removeClass('d-none');
			
			var htmlItem = `
				<a href="javascript:;" class="kt-media kt-media--lg mr-10 position-relative kt-spinner kt-spinner--sm kt-spinner--brand">
                    <img nh-image="selected" src="${urlImage}">
                    <span nh-btn="clear-image" class="btn-clear-image-album" title="">
                        <i class="fa fa-times"></i>
                    </span>
                </a>`;

            wrapAlbum.append(htmlItem);

            var uploaded = params.uploaded || 0;
			if(uploaded > 0) wrapElement.find('li:last-child').removeClass('loading');
		},
	},
}

nhCommentByRecord.init();