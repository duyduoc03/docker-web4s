"use strict";

var nhListComment = function() {
    var formElement;

    var options = {
        data: {
            type: 'remote',
            source: {
                read: {
                    url: adminPath + '/comment/list/json',
                    params: {},
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
                width: 18,
                type: 'number',
                selector: {class: 'select-record kt-checkbox bg-white'},
                textAlign: 'center',
                autoHide: false,
                sortable: false,
            },
            {
                field: 'full_name',
                title: nhMain.getLabel('khach_hang'),
                width: 200,
                autoHide: false,
                template: function(row) {
                    var fullName = row.full_name || ''
                    var email = row.email || '';
                    var phone = row.phone || '';
                    var isAdmin = row.is_admin || '';

                    var fullNameLabel = ``;
                    var adminLabel = ``;
                    if(isAdmin) {
                        adminLabel = `
                            <span class="kt-badge kt-badge--danger kt-badge--inline">
                                Admin
                            </span>`
                    }
                    if(fullName != ''){
                        fullNameLabel = `
                            <p class="mb-5">
                                <i class="fa fa-user mr-5"></i>
                                ${fullName}
                                ${adminLabel}
                            </p>`;
                    }

                    var phoneLabel = ``;
                    if(phone != ''){
                        phoneLabel = `
                            <p class="mb-5">
                                <i class="fa fa-phone mr-5"></i>
                                ${phone}
                            </p>`;
                    }

                    var emailLabel = ``;
                    if(email != ''){
                        emailLabel = `
                            <p class="mb-0">
                                <i class="fa fa-envelope mr-5"></i>
                                ${email}
                            </p>`;
                    }

                    return `
                        <div class="kt-user-card-v2 kt-user-card-v2--uncircle">
                            <div class="kt-user-card-v2__details lh-1-5">
                                ${fullNameLabel}
                                ${phoneLabel}
                                ${emailLabel}
                            </div>
                        </div>`;
                }
            },
            {
                field: 'content',
                title: nhMain.getLabel('noi_dung'),
                width: 300,
                sortable: false,
                template: function(row) {
                    var content = row.content || '';
                    var images = row.images ? row.images : [];
                    var typeComment = row.type_comment || '';
                    var rating = row.rating || 0;
                    var recordName = row.record_name || '';
                    var type = row.type || '';
                    var url = row.url || '';
                    var parentId = row.parent_id || '';

                    var starLabel = '';
                    if(typeComment == 'rating'){
                        for (var i = 0; i < rating; i++) {
                            starLabel += '<i class="fa fa-star text-warning"></i>';
                        }
                    }

                    if(content != '' && content.length > 100) {
                        content = content.substring(0, 100);
                        content += '...';
                    }

                    if(parentId != ''){
                        content = `<i class="flaticon-reply"></i> ${content}`;    
                    }
                    
                    var htmlImages = '';
                    $.each(images, function(index, urlImage) {
                        var imageHtml = ``;
                        if(index <= 3){
                            imageHtml = '<img src="'+ cdnUrl + nhMain.utilities.getThumbs(urlImage, 150) +'">';
                        }

                        if(index == 4){
                            imageHtml = `<span class="symbol-label font-weight-bold">+${(images.length - 4).valueOf()}</span>`;
                        }

                        var classDisplay = '';
                        if(index > 4){
                            classDisplay = 'd-none';
                            imageHtml = '';
                        }

                        htmlImages += `
                            <a class="${classDisplay} symbol symbol-circle" href="${cdnUrl + urlImage}" data-lightbox="${row.id}">
                                ${imageHtml}
                            </a>`;
                    });

                    return `
                        <p class="mb-0">
                            ${starLabel}
                        </p>
                        <p class="mb-5">
                            ${content}
                        </p>
                        <p class="mb-0">
                            <div class="symbol-group symbol-hover">${htmlImages}</div>
                        </p>`;
                }
            },          
            {
                field: 'type',
                title: nhMain.getLabel('bai_viet') + '/' + nhMain.getLabel('san_pham'),
                sortable: false,
                width: 250,
                template: function(row) {
                    var type = row.type || '';
                    var recordId = row.foreign_id || '';
                    var recordName = row.record_name || '';
                    var url = row.url || '';
                    var typeComment = row.type_comment || '';

                    
                    var badgeRating = ``;
                    if(typeComment == 'rating'){
                        badgeRating = `
                            <span class="kt-badge kt-badge--warning kt-badge--inline">
                                ${nhMain.getLabel('danh_gia')}
                            </span>`;
                    }

                    var labelRecord = `<p class="mb-5">${recordName} ${badgeRating}</p>`;

                    var urlEdit = ``;
                    if(type == _PRODUCT_DETAIL && recordId != ''){
                        urlEdit = `${adminPath}/product/update/${recordId}#comment-record`;                      
                    }

                    if(type == _ARTICLE_DETAIL && recordId != ''){
                        urlEdit = `${adminPath}/article/update/${recordId}#comment-record`;
                    }

                    var editRecord = `
                        <a href="${urlEdit}" target="_blank" class="mr-5">
                            <i class="fa fa-edit"></i>
                            ${nhMain.getLabel('cap_nhat')}
                        </a>
                        <a href="/${url}" target="_blank">
                            <i class="fa fa-eye"></i>
                            ${nhMain.getLabel('xem')}
                        </a>`;

                    var html = `${labelRecord} ${editRecord}`;
                    return html;
                }
            },
            {
                field: 'status',
                title: nhMain.getLabel('trang_thai'),               
                width: 100,
                autoHide: false,
                template: function(row) {
                    var status = row.status || '';                    
                    var created = row.created || '';
                    var htmlStatus = nhList.template.statusComment(row.status);
                    
                    var htmlCreated = `
                        <div class="pt-5">
                            <i class="fs-11">
                                ${nhMain.utilities.parseIntToDateTimeString(created)}
                            </i>
                        </div>`;
                    
                    return htmlStatus + htmlCreated ;
                },
            },         
            {
                field: 'action',
                title: '',
                width: 30,
                autoHide: false,
                sortable: false,
                template: function(row){     
                    return `
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">
                                <i class="flaticon-more"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right pt-5 pb-5">
                                <a class="dropdown-item nh-change-status" href="javascript:;" data-id="${row.id}" data-status="1">
                                    <span class="text-success">
                                    <i class="fas fa-check-circle fs-14 mr-10"></i>
                                        ${nhMain.getLabel('duyet')}
                                    </span>
                                </a>
                                <a class="dropdown-item nh-change-status" href="javascript:;" data-id="${row.id}" data-status="0">
                                    <span class="text-warning">
                                    <i class="fas fa-times-circle fs-14 mr-10"></i>
                                        ${nhMain.getLabel('khong_duyet')}
                                    </span>
                                </a>
                                <a class="dropdown-item nh-delete" href="javascript:;" data-id="${row.id}">
                                    <span class="text-danger"><i class="fas fa-trash-alt fs-14 mr-10"></i>
                                        ${nhMain.getLabel('xoa')}
                                    </span>
                                </a>
                            </div>
                        </div>`;
                }
            }
        ]
    }

    var comment = {
        param: {},
        pagination: {},

        modalElement: $('#modal-comment'),
        listImageAlbum: null,
        config:{
            max_number_files: 10,
            expires_cookie: 10,      
        },
        template:{
            listImageSelect: '<div class="list-image-album"></div>',
            imageSelect: '\
                <span class="kt-spinner kt-spinner--sm kt-spinner--brand kt-spinner--center item-image kt-media kt-media--lg mr-10 position-relative">\
                    <img src="" />\
                    <span class="btn-clear-image-album" title="' + nhMain.getLabel('xoa_anh') +'">\
                        <i class="fa fa-times"></i>\
                    </span>\
                </span>',
        },

        init: function() {
            var self = this;

            if(self.modalElement.length == 0) return false;

            $('.kt-selectpicker').selectpicker();

            $('.kt_datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                autoclose: true,
            });

            lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });

            self.loadListComment();
        },

        loadListComment: function(){
            var self = this;
            
            var datatable = $('.kt-datatable').KTDatatable(options);

            $('#nh_status').on('change', function() {
                datatable.search($(this).val(), 'status');
            });
            
            $('#subject').on('change', function() {
                datatable.search($(this).val(), 'subject');
            });
            
            $('#nh_comment_type').on('change', function() {
                datatable.search($(this).val(), 'type_comment');
            });

            $('#type').on('change', function() {
                datatable.search($(this).val(), 'type');
            });
            
            $('#create_from').on('change', function() {
                datatable.search($(this).val(), 'create_from');
            });
            
            $('#create_to').on('change', function() {
                datatable.search($(this).val(), 'create_to');
            });

            $(document).on('click', '[nh-export]', function(e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var page = typeof(datatable.getCurrentPage()) != _UNDEFINED ? datatable.getCurrentPage() : 1;

                var data_filter = {
                    lang: nhMain.lang,
                    keyword: $('#nh-keyword').val(),
                    subject: $('#subject').val(),
                    status: $('#nh_status').val(),
                    create_from: $('#create_from').val(),
                    create_to: $('#create_to').val(),
                    type: $('#type').val(),
                }

                nhMain.callAjax({
                    url: adminPath + '/comment/list/json',
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
            
            // event delete and change status on list
            nhList.eventDefault(datatable, {
                url: {
                    delete: adminPath + '/comment/delete',
                    status: adminPath + '/comment/change-status'
                }
            });
        },

        listCommentEvent: function(){
            var self = this;

            $(document).on('click', '[admin-reply]', function(e) {
                var id = $(this).data('id');
                var parent_id = $(this).data('parent-id');

                if(typeof(id) == _UNDEFINED || id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                nhMain.callAjax({
                    async: true,
                    url: adminPath + '/comment/view-comment/' + id,
                    dataType : 'html',
                    data:{
                        id: id,
                        parent_id: parent_id
                    }
                }).done(function(response){
                    self.modalElement.find('.modal-body').html(response);
                    self.modalElement.modal('show');

                    self.listImageAlbum = self.modalElement.find('.list-image-album');
                    formElement = self.modalElement.find('form#main-form');
                    comment.attachmentEvent();
                });
            });

            $(document).on('click', '.btn-list-comment', function(e) {
                var id = $(this).data('id');
                var parent_id = $(this).data('parent-id');

                if(typeof(id) == _UNDEFINED || id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                nhMain.callAjax({
                    async: true,
                    url: adminPath + '/comment/comment-modal',
                    dataType : 'html',
                    data:{
                        id: id,
                        parent_id: parent_id
                    }
                }).done(function(response){
                    self.modalElement.find('.modal-body').html(response);
                    self.modalElement.modal('show');
                });
            });
        },

        attachmentEvent:function() {
            var self = this;

            if(self.listImageAlbum.length == 0) return false;

            $(document).on('click', '#nh-trigger-upload', function(e) {
                var boxComment = $(this).closest('.kt-todo__panel');
                if(boxComment.length == 0) return;

                boxComment.find('input.nh-input-comment-images').trigger('click');
            });

            $(document).on('change', '.nh-input-comment-images', function(e) {
                var typeComment = $('#type-comment').val();
                self.showImagesSelect(this, typeComment);
            });

            $(document).on('click', '.btn-clear-image-album', function(e) {
                $(this).closest('span.item-image').remove();
                self.setValueImages();
            });
        },

        showImagesSelect: function(input = null, typeComment = null) {
            var self = this;
            if(input == null || typeof(input.files) == _UNDEFINED){
                return false;
            }

            if(self.listImageAlbum.length == 0) return false;
            self.listImageAlbum.css('display', '');
            self.listImageAlbum.html('');

            $.each(input.files, function(index, file) {
                if(index >= self.config.max_number_files) return;

                var fileReader = new FileReader();
                fileReader.readAsDataURL(file);
                fileReader.onload = function(e) {
                    self.appendImageSelect(fileReader.result);
                }
            });

            // return false;
            $.each(input.files, function(index, file) {
                if(index >= self.config.max_number_files) return;

                var formData = new FormData();
                formData.append('file', file);
                formData.append('path', typeComment);

                nhMain.callAjax({
                    async: true,
                    url: adminPath + '/comment/upload-file',
                    data: formData,
                    contentType: false,
                    processData: false,
                }).done(function(response) {
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var data = typeof(response.data) != _UNDEFINED ? response.data : {};

                    if (code == _SUCCESS && !$.isEmptyObject(data)) {
                        var urlImage = typeof(data.url) != _UNDEFINED ? data.url : null;
                        var itemElement = self.listImageAlbum.find('span.item-image:eq('+ index +')');
                        if(itemElement.length > 0){
                            itemElement.removeClass('kt-spinner');
                            itemElement.find('img').attr('src', cdnUrl + urlImage);
                        }
                    }
                    self.setValueImages();
                });
            });            
        },

        appendImageSelect: function(urlImage = null){
            var self = this;
            
            if(self.listImageAlbum.length == 0){
                return false;
            }

            if(urlImage == null || typeof(urlImage) == _UNDEFINED || urlImage.length == 0){
                return false;
            }

            self.listImageAlbum.append(self.template.imageSelect);
            self.listImageAlbum.find('span.item-image:last-child img').attr('src', urlImage);
        },

        setValueImages: function(){
            var self = this;
            var listImages = [];

            self.listImageAlbum.find('span.item-image').each(function(index) {
                if($(this).find('img').length > 0){
                    listImages.push($(this).find('img').attr('src'));
                }
            });

            $('#images').val(JSON.stringify(listImages));
        },

        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();

                var check = true;       
                $('.list-image-album').find('span.item-image').each(function(index){
                    if($(this).hasClass('kt-spinner')){
                        toastr.error(nhMain.getLabel('vui_long_cho_he_thong_dang_tai_anh_binh_luan'));
                        check = false;
                    }
                });

                if(check){
                    nhMain.initSubmitForm(formElement, $(this));    
                }
                
            });
        },
    }

    return {
        init: function() {
            comment.init();
            comment.listCommentEvent();
            comment.submitEvent();
            importExcel.init();
        }
    }
}();

var importExcel = {
    idModal: '#import-excel-modal',
    init:function() {
        var self = this;

        $(document).on('click', '#btn-import-excel:not(.disabled)', function(e) {
            var btnImport = $(this);

            // show loading
            btnImport.find('.icon-spinner').removeClass('d-none');
            $(this).addClass('disabled');

            var file_input = $('#excel_file');
            var file_data = file_input[0].files;
            if(file_input.length == 0){
                nhMain.showLog(nhMain.getLabel('khong_tim_thay_du_lieu_file'));
            }

            var formData = new FormData();
            $.each(file_data, function(index, file) {
                formData.append("excel_file", file);                
            });

            nhMain.callAjax({
                async: true,
                url: adminPath + '/comment/import-excel',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(response) {
                KTApp.unblockPage();                    

                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : {};
                var folder = typeof(data.folder) != _UNDEFINED ? data.folder : '';
                
                if (code == _SUCCESS) {
                    self.import(0, folder, function(e) {
                        // remove loading
                        btnImport.find('.icon-spinner').addClass('d-none');
                        $(this).removeClass('disabled');
                    });
                }else{
                    $(self.idModal).find('.alert.alert-danger').removeClass('d-none');
                    $(self.idModal).find('.alert.alert-danger').text(message);
                    toastr.error(message);
                }
            });

            return false;
        });
        
        $('#import-excel-modal').on('hidden.bs.modal', function () {
            location.reload();
        });
    },

    import: function(page = 0, folder = null, callback = null){
        var self = this;

        if (typeof(callback) != 'function') {
            callback = function () {};
        }

        var data = {
            page: page,
            folder: folder
        }

        nhMain.callAjax({
            url: adminPath + '/comment/process-import-excel',
            data: data
        }).done(function(response) {
            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
            var data = typeof(response.data) != _UNDEFINED ? response.data : '';
            var folder = typeof(data.folder) != _UNDEFINED ? data.folder : '';
            var percent = typeof(data.percent) != _UNDEFINED ? data.percent : 0;

            if (code == _SUCCESS) {
                if(typeof(data.product) != _UNDEFINED && data.product > 0){
                    $('#current-item-excel').html(data.product);
                }

                $(self.idModal).find('.progress').removeClass('d-none');
                $(self.idModal).find('.progress .progress-bar').css('width', `${percent}%`);
                $(self.idModal).find('.progress .progress-bar').text(`${percent}%`);

                if(typeof(data.continue) != _UNDEFINED && data.continue){
                    self.import(data.page, folder, callback);
                }else{          
                    $(self.idModal).find('.alert.alert-success').removeClass('d-none'); 
                    toastr.success(message);
                    callback(response);
                }

            } else {
                $(self.idModal).find('.alert.alert-danger').removeClass('d-none');
                $(self.idModal).find('.alert.alert-danger').text(message);
                toastr.error(message);
            }
        });
    }
}


$(document).ready(function() {
    if($('body[path-menu="comment"]').length > 0) {
        nhListComment.init();    
    }  
});