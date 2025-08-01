"use strict";

var nhSeoSitemap = function () {

    var formEl;

    var initSubmit = function() {
        $(document).on('click', '#btn-save-sitemap', function(e) {
            e.preventDefault();

            nhMain.initSubmitForm(formEl, $(this));
        });
    }

    return {
        init: function() {
            formEl = $('#sitemap-form');            
            initSubmit();

            $(document).on('change', '[name="apply_sitemap"]', function(e){
                e.preventDefault();

                var value = $(this).val() || '';

                $('#wrap-manual-sitemap').collapse(value == 1 ? 'show' : 'hide');
            });

            nhMain.selectMedia.dropzoneUpload({
                id: 'sitemap',
                url: adminPath + '/upload-file-sitemap',
                ext: '.xml',
                maxFile: 1,
            }, null, function() {
                toastr.info(nhMain.getLabel('tai_len_thanh_cong'));
            });
        }
    };
}();

$(document).ready(function() {
    nhSeoSitemap.init();
});