"use strict";

var nhSettingLocalesSystem = {
    wrapElement: $('#locales-system'),
    page: 1,
    typeData: 'system_po',  // Khởi tạo type mặc định là 'system_po'
    keyword: '', 
    formEl: null,
    typeElement: null,
    currentActiveTab: null, // Lưu tab đang active
    init: function(){
        var self = this;
        self.formEl = $('[nh-form="list-locales"]');
        
        if(self.wrapElement.length == 0) return;

        self.load();
        self.events();

        // dich tự động dữ liệu
        self.translates.init();

        $('.kt-selectpicker').selectpicker();
    },
    
    // lấy tab đang active
    getActiveTabType: function() {
        return this.wrapElement.find('.nav-link.active').attr('nh-type-data') || this.typeData;
    },
    
    // lấy content của tab đang active
    getActiveTabContent: function() {
        var activeTab = this.wrapElement.find('.nav-link.active');
        var tabElement = activeTab.attr('nh-tab-element');
        return this.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] [nh-form-table]');
    },
    
    // lấy keyword từ tab đang active
    getActiveKeyword: function() {
        var activeTab = this.wrapElement.find('.nav-link.active');
        var tabElement = activeTab.attr('nh-tab-element');
        return this.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] input[name="keyword"]').val() || '';
    },
    
    // kiểm tra tab đã load dữ liệu chưa
    isTabLoaded: function(tabElement) {
        var formWrapper = this.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] [nh-form-table]');
        return formWrapper.length > 0 && $.trim(formWrapper.html()) !== '';
    },
    
    // reset search và load dữ liệu mới
    resetSearchAndLoad: function() {
        this.page = 1;
        this.keyword = '';
        
        // Reset input search về rỗng
        var activeTab = this.wrapElement.find('.nav-link.active');
        var tabElement = activeTab.attr('nh-tab-element');
        this.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] input[name="keyword"]').val('');
        
        this.load();
    },
        
    events: function(){
        var self = this;
        // click vào radio chọn loại file locales file js hoặc file po
        self.wrapElement.on('change', 'input[name="type_file"]', function() {
            var _typeData = $(this).attr('data-type');
            
            // Cập nhật type load view
            self.wrapElement.find('[nh-type-load-view]').attr('nh-type-load-view', _typeData);
            
            // Chỉ update nếu type thay đổi
            if (self.typeData !== _typeData) {
                self.typeData = _typeData;
                self.resetSearchAndLoad();
            }
        });

        // click vào radio chọn loại danh mục sản phẩm, bài viết
        self.wrapElement.on('change', 'input[name="type_category"]', function() {
            var _typeData = $(this).attr('data-type');
            
            // Cập nhật type load view
            self.wrapElement.find('[nh-type-load-view]').attr('nh-type-load-view', _typeData);
            
            // Chỉ update nếu type thay đổi
            if (self.typeData !== _typeData) {
                self.typeData = _typeData;
                self.resetSearchAndLoad();
            }
        });


        // tìm kiếm trong tab active
        $(document).on('click', '[nh-btn-action="locale-search"]', function(e){
            e.preventDefault();
            self.page = 1;
            self.keyword = self.getActiveKeyword();
            self.load();
            return false;
        });

        // reload dữ liệu tab active
        $(document).on('click', '[nh-btn-action="locale-reload"]', function(e){
            e.preventDefault();
            self.resetSearchAndLoad();
            return false;
        });

        // phân trang
        $(document).on('click', '.kt-pagination__links li:not(.kt-datatable__pager-link--disabled , .kt-pagination__link--active) a', function(e){
            e.preventDefault();
            self.page = parseInt($(this).attr('data-page'));
            self.load();
        });

        // click tab khi chưa có dữ liệu thì load dữ liệu 
        $(document).on('click', '[nh-tab-element]', function() {
            var _typeData = $(this).attr('nh-type-data');
            var _tabElement = $(this).attr('nh-tab-element');
            
            // Cập nhật type data và view
            self.typeData = _typeData;
            self.wrapElement.find('[nh-type-load-view]').attr('nh-type-load-view', _typeData);
            
            // Chỉ load dữ liệu nếu tab chưa có dữ liệu
            if (!self.isTabLoaded(_tabElement)) {
                self.page = 1;
                self.keyword = '';
                self.load();
            }
            
            // Lưu tab đang active
            self.currentActiveTab = _tabElement;
        });
        
        //khi nhập dữ liệu vào input thì thêm class is-updated vào input và label
        $(document).on('change', '[nh-input-change-data]', function() {
            var itemValue = $(this);
            var currentValue = itemValue.val();
            var originalValue = itemValue.data('original-value');
            
            if (currentValue !== originalValue) {
                itemValue.addClass('is-updated');
            } else {
                itemValue.removeClass('is-updated');
            }
        });

        // lưu dữ liệu
        $(document).on('click', '.btn-save', function(e) {
            e.preventDefault();
            
            var formEl = $('#list-locales');
            var _rows = formEl.find('[nh-content-element].active tbody tr');
            var data = [];
            var type_view = formEl.find('[nh-type-load-view]').attr('nh-type-load-view');
            _rows.each(function() {
                var _row = $(this);
                var inputsUpdated = _row.find('[nh-input-change-data].is-updated');
                
                if (inputsUpdated.length === 0) return; 
                
                var id = inputsUpdated.first().attr('nh-data-id');
                var type = inputsUpdated.first().attr('nh-data-type');
                var code = inputsUpdated.first().attr('nh-data-code');

                var translations = {};
                inputsUpdated.each(function() {
                    var $input = $(this);
                    var langCode = $input.attr('nh-data-lang');
                    var currentValue = $input.val();
                    translations[langCode] = currentValue;
                });
                
                data.push({
                    id: id,
                    type: type,
                    code: code,
                    translations: translations
                });
            });
            if (data.length === 0) {
                toastr.error(nhMain.getLabel('khong_co_thay_doi_nao'));
                return;
            }
            
            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: adminPath + '/setting/save-locale-translation',
                data: {
                    data: data,
                    type_view: type_view
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                
                if (code == _SUCCESS) {
                    toastr.info(nhMain.getLabel('cap_nhat_thanh_cong'));
                    KTApp.unblockPage();
                    $('[nh-input-change-data].is-updated').each(function() {
                        var $textarea = $(this);
                        var key = $textarea.attr('nh-key');
                        var $label = $textarea.closest('.form-group').find('label');
                        
                        $textarea
                            .removeClass('is-updated')
                            .addClass('border-success');
                        
                    });
                    $('textarea[nh-lang-key]').each(function() {
                        $(this).data('original-value', $(this).val());
                    });
                } else {
                    toastr.error(message);
                    KTApp.unblockPage();
                }
            });

        });
    },
    
    load: function(){
        var self = this;
        KTApp.blockPage(blockOptions);
        
        // Lấy tab đang active và content tương ứng
        var typeElement = self.getActiveTabType();
        var activeContent = self.getActiveTabContent();

        //change lại input type data 
        self.wrapElement.find('[nh-type-load-data]').attr('nh-type-load-data', self.typeData);

        var formData = self.formEl.serialize();
        formData = formData + '&page=' + self.page +  '&type=' + self.typeData + '&filter[keyword]=' + self.keyword;
        nhMain.callAjax({
            async: false,
            dataType: 'html',
            url: adminPath + '/setting/get-list-locale-label',
            data: formData,
            async: true      
        }).done(function(response) {
            // Load dữ liệu vào tab đang active
            activeContent.html(response);
            KTApp.unblockPage();
        });
    },
    
    translates: {
        init: function(){
            var self = this;
            self.events();
        },
        events: function(){
            var self = this;
            // Dịch từng input một
            $(document).on('click', '[nh-btn="data-extend-translate"]', function(e) {
                e.preventDefault();
                
                var langDefault = $(this).attr('nh-language-default');
                if(typeof(langDefault) == _UNDEFINED || langDefault.length == 0) {
                    toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh'));
                    return;
                }

                var tr = $(this).closest('tr');
                var inputTranslate = tr.find('[nh-data-lang="'+ langDefault +'"]');
                var label = inputTranslate.val();
                
                if(typeof(label) == _UNDEFINED || label.length == 0) {
                    toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh'));
                    return;
                }

                // Lấy ngôn ngữ cần dịch từ input hiện tại
                var currentLang = $(this).closest('td').find('input').attr('nh-data-lang');
                if(typeof(currentLang) == _UNDEFINED || currentLang.length == 0) {
                    toastr.error(nhMain.getLabel('khong_tim_thay_ngon_ngu_can_dich'));
                    return;
                }

                // Nếu đang ở ngôn ngữ mặc định thì không cần dịch
                if(currentLang === langDefault) {
                    toastr.error(nhMain.getLabel('ban_khong_the_dich_sang_ngon_ngu_mac_dinh'));
                    return;
                }

                KTApp.blockPage(blockOptions);

                nhSettingLocalesSystem.translates.translateLabel(label, currentLang, function(translates){
                    if(translates && translates[currentLang]) {
                        var translatedText = translates[currentLang];
                        tr.find('[nh-data-lang="'+ currentLang +'"]').val(translatedText);
                        tr.find('[nh-data-lang="'+ currentLang +'"]').addClass('is-updated');
                        toastr.info(nhMain.getLabel('dich_thanh_cong'));
                    } else {
                        toastr.error(nhMain.getLabel('khong_the_dich_sang_ngon_ngu_nay'));
                    }
                    KTApp.unblockPage();
                });
            });

            // dich label cột
            $(document).on('click', '[nh-btn="data-extend-translate-column"]', function(e) {
                var langDefault = $(this).attr('nh-language-default');				
                if(typeof(langDefault) == _UNDEFINED || langDefault.length == 0) return;
                
                var lang = $(this).attr('nh-language-column');
                if(typeof(lang) == _UNDEFINED || lang.length == 0) return;
            
                // Chỉ lấy table của tab đang active
                var activeTab = nhSettingLocalesSystem.wrapElement.find('.nav-link.active');
                var tabElement = activeTab.attr('nh-tab-element');
                var table = nhSettingLocalesSystem.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] table');
                var rows = table.find('tbody tr');
                var translateData = []; // Mảng chứa dữ liệu cần dịch
                var errorIds = []; // Lưu các id không dịch được
            
                KTApp.blockPage(blockOptions);
                rows.each(function() {
                    var row = $(this);
                    var inputDefault = row.find('[nh-data-lang="'+ langDefault +'"]');
                    var inputTarget = row.find('[nh-data-lang="'+ lang +'"]');
                    var label = inputDefault.val();
                    var rowId = inputDefault.attr('nh-data-id') || row.attr('data-id') || '';
                    
                    // Chỉ dịch những input chưa có giá trị
                    if(inputTarget.val().trim() === '') {
                        if(typeof(label) == _UNDEFINED || label.length == 0) {
                            if(rowId) errorIds.push(rowId);
                        } else {
                            translateData.push({
                                id: rowId,
                                text: label,
                                targetInput: inputTarget
                            });
                        }
                    }
                });
                
                // Nếu không có gì để dịch
                if(translateData.length === 0) {
                    KTApp.unblockPage();
                    if(errorIds.length > 0) {
                        toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh') + ' ' + errorIds.join(', '));
                    } else {
                        toastr.info(nhMain.getLabel('dich_thanh_cong'));
                    }
                    return;
                }
                
                var textsToTranslate = translateData.map(function(item) {
                    return item.text;
                });
                
                nhSettingLocalesSystem.translates.translateMultipleLabels(textsToTranslate, lang, function(translations) {
                    if(translations && translations.length > 0) {
                        // Cập nhật các input với kết quả dịch
                        translateData.forEach(function(item, index) {
                            if(translations[index]) {
                                item.targetInput.val(translations[index]);
                                item.targetInput.addClass('is-updated');
                            }
                        });
                        toastr.info(nhMain.getLabel('dich_thanh_cong') + ' ' + translations.length + ' ' + nhMain.getLabel('muc'));
                    }
                    
                    KTApp.unblockPage();
                    
                    if(errorIds.length > 0) {
                        toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh') + ' ' + errorIds.join(', '));
                    }
                });
            });

            // dich tất cả dữ liệu trong trang hiện tại
            $(document).on('click', '[nh-btn="data-extend-translate-all"]', function(e) {
                var langDefault = $(this).attr('nh-language-default');				
                if(typeof(langDefault) == _UNDEFINED || langDefault.length == 0) return;
                
                // Chỉ lấy table của tab đang active
                var activeTab = nhSettingLocalesSystem.wrapElement.find('.nav-link.active');
                var tabElement = activeTab.attr('nh-tab-element');
                var table = nhSettingLocalesSystem.wrapElement.find('.tab-pane[nh-content-element="' + tabElement + '"] table');
                var rows = table.find('tbody tr');
                var translateData = []; // Mảng chứa dữ liệu cần dịch
                var errorIds = []; // Lưu các id không dịch được
                var languages = []; // Mảng chứa các ngôn ngữ cần dịch
                
                KTApp.blockPage(blockOptions);
                
                // Thu thập tất cả dữ liệu cần dịch
                rows.each(function() {
                    var row = $(this);
                    var inputDefault = row.find('[nh-data-lang="'+ langDefault +'"]');
                    var label = inputDefault.val();
                    var rowId = inputDefault.attr('nh-data-id') || row.attr('data-id') || '';
                    
                    if(typeof(label) == _UNDEFINED || label.length == 0) {
                        if(rowId) errorIds.push(rowId);
                        return;
                    }
                    
                    // Tìm tất cả các input ngôn ngữ khác trong row này
                    row.find('[nh-data-lang]').each(function() {
                        var input = $(this);
                        var lang = input.attr('nh-data-lang');
                        
                        // Bỏ qua ngôn ngữ mặc định và những input đã có giá trị
                        if(lang === langDefault || input.val().trim() !== '') {
                            return;
                        }
                        
                        // Thêm ngôn ngữ vào danh sách nếu chưa có
                        if(languages.indexOf(lang) === -1) {
                            languages.push(lang);
                        }
                        
                        translateData.push({
                            id: rowId,
                            text: label,
                            targetInput: input,
                            lang: lang
                        });
                    });
                });
                
                // Nếu không có gì để dịch
                if(translateData.length === 0) {
                    KTApp.unblockPage();
                    if(errorIds.length > 0) {
                        toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh') + ' ' + errorIds.join(', '));
                    } else {
                        toastr.info(nhMain.getLabel('tat_ca_du_lieu_da_duoc_dich'));
                    }
                    return;
                }
                
                // Nhóm dữ liệu theo ngôn ngữ để dịch
                var totalTranslated = 0;
                var totalLanguages = languages.length;
                var completedLanguages = 0;
                
                languages.forEach(function(lang) {
                    var langData = translateData.filter(function(item) {
                        return item.lang === lang;
                    });
                    
                    var textsToTranslate = langData.map(function(item) {
                        return item.text;
                    });
                    
                    nhSettingLocalesSystem.translates.translateMultipleLabels(textsToTranslate, lang, function(translations) {
                        if(translations && translations.length > 0) {
                            // Cập nhật các input với kết quả dịch
                            langData.forEach(function(item, index) {
                                if(translations[index]) {
                                    item.targetInput.val(translations[index]);
                                    item.targetInput.addClass('is-updated');
                                    totalTranslated++;
                                }
                            });
                        }
                        
                        completedLanguages++;
                        
                        // Kiểm tra xem đã hoàn thành tất cả ngôn ngữ chưa
                        if(completedLanguages === totalLanguages) {
                            KTApp.unblockPage();
                            
                            if(totalTranslated > 0) {
                                toastr.info(nhMain.getLabel('da_dich_thanh_cong') + ' ' + totalTranslated + ' ' + nhMain.getLabel('muc'));
                            }
                            
                            if(errorIds.length > 0) {
                                toastr.error(nhMain.getLabel('khong_co_du_lieu_ngon_ngu_mac_dinh') + ' ' + errorIds.join(', '));
                            }
                        }
                    });
                });
            });
            
        },
        translateLabel: function(label = null, target_lang = null, callback = null){
            var self = this;
            if(label == null || label.length == 0 || target_lang == null) return;
            
            if (typeof(callback) != 'function') {
                callback = function () {};
            }
            
            nhMain.callAjax({
                async: true,
                url: adminPath + '/setting/translate-label',
                data: {
                    label: label,
                    target_lang: target_lang
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : {};

                callback(data);
            });
        },
        translateMultipleLabels: function(labels, lang, callback) {
            var self = this;
            if(labels == null || labels.length == 0) return;
    
            if (typeof(callback) != 'function') {
                callback = function () {};
            }
            
            nhMain.callAjax({
                async: true,
                url: adminPath + '/setting/translate-multiple-labels',
                data: {
                    labels: labels,
                    lang: lang
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : {};
    
                callback(data);
            });
        }
    }
}


$(document).ready(function() {
    nhSettingLocalesSystem.init();
});