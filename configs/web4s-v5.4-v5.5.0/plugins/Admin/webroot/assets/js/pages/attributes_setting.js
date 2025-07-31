"use strict";

var nhAttributesCategory = function() {
    var wizardEl;
    var formEl;
    var formApplyAttributes;
    var wizard;

    var initWizard = function () {
        wizard = new KTWizard('kt_wizard', {
            startStep: 1,
            clickableSteps: true,
        });
    }
    var category = {
        param: {},
        pagination: {},
        type: 'product',
        listEl: KTUtil.getByID('kt_todo_list'),
        init: function(){
            var self = this;

            self.event();
        },
        event: function(){
            var self = this;
            $(document).on('click', '[nh-category-select="attribute"]:checked', function() {
                
                var category_id = $(this).val();
                var type = $(this).attr('nh-type');

                if(typeof(category_id) == _UNDEFINED || category_id.length == 0 || typeof(type) == _UNDEFINED){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                self.loadListAttribute(category_id, type);
            });
            
            
            $(document).on('click','input[nh-attribute-select="option"]', function() {
                var type = $(this).attr('nh-type');
                var category_id = $(this).attr('nh-category');

                var attributes_type = $(`[nh-list-attributes-${type}]`);
                var wrapAttributes = $(this).closest(attributes_type);
                
                var attribute_ids =  wrapAttributes.find('input[nh-attribute-select="option"]:checked').map(function() {
                    return this.value;
                }).get();
                self.loadListOption(attribute_ids, type, category_id);
            });

        },
        loadListAttribute: function(category_id = null, type = null ) {
            var self = this;

            if(typeof(category_id) == _UNDEFINED || category_id.length == 0 || typeof(type) == _UNDEFINED){
                toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                return false;
            }

            var wrapElement = $(`#wrap-attributes-${type}`);
            if(wrapElement.length == 0) return;

            nhMain.callAjax({
                url: adminPath + '/setting/attribute/load-attributes-by-category',
                data:{
                    category_id: category_id,
                    type: type
                },
                dataType : 'html'
            }).done(function(response){
                wrapElement.html(response);
                var wrapAttributes = $(`[nh-list-attributes-${type}]`);
                var attribute_ids = [];
                if (wrapAttributes.length != 0) {
                    wrapAttributes.find('[nh-attribute-select="option"]:checked').each(function(index) {
                        var attribute_id = $(this).val();
                        var type = $(this).attr('nh-type');
                        var category_id = $(this).attr('nh-category');
                        if(typeof(attribute_id) == _UNDEFINED || attribute_id.length == 0 || typeof(type) == _UNDEFINED){
                            toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                            return false;
                        }
                        
                        if(attribute_id > 0){
                            attribute_ids.push(attribute_id);
                        }
                    });
                    self.loadListOption(attribute_ids, type, category_id);
                }
            });
        },

        loadListOption: function(attribute_ids = [], type = null , category_id = null) {
            var wrapElement = $(`#wrap-options-${type}`);
            if(wrapElement.length == 0) return;
            nhMain.callAjax({
                url: adminPath + '/setting/attribute/load-options-by-category',
                data:{
                    attribute_ids: attribute_ids,
                    type: type,
                    category_id : category_id
                },
                dataType : 'html'
            }).done(function(response){
                wrapElement.html(response);
            });
        },
        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();
                var formElement = $('.kt-wizard-v2__content[data-ktwizard-state="current"] form');

                KTApp.blockPage(blockOptions);
                var formData = formElement.serialize();
                
                nhMain.callAjax({
                    url: formElement.attr('action'),
                    data: formData
                }).done(function(response) {
                    // hide loading
                    KTApp.unblockPage();

                    //show message and redirect page
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                    var data = typeof(response.data) != _UNDEFINED ? response.data : {};
                    toastr.clear();
                    if (code == _SUCCESS) {
                        toastr.info(message);              
                    } else {
                        toastr.error(message);
                    }
                });

            });
        },
    }

    return {
        init: function() {
            wizardEl = KTUtil.get('kt_wizard');
            initWizard();
            category.init();
            category.submitEvent();
        }
    }
}();

$(document).ready(function() {
    nhAttributesCategory.init();    
});