
<form nh-form="list-template" action="{ADMIN_PATH}/setting/get-list-locale-label" method="POST" autocomplete="off" class="h-100">
    <div class="kt-list-locales">
        <div class="top-content-locales">
            <div class="type-locales">
                <div class="row align-items-center">
                    <div class="col-lg-3 ">
                        <div class="bnt-search-reload d-flex align-items-center">
                            <div class="kt-input-icon kt-input-icon--right">
                                <input id="nh-keyword" name="keyword" type="text"
                                    class="form-control form-control-sm"
                                    placeholder="{__d('admin', 'tim_kiem')}..." autocomplete="off">
                                
                                <span nh-btn-action="locale-search" type="submit" class="input-group-append kt-input-icon__icon kt-input-icon__icon--right">
                                    <span class="btn btn-sm btn-brand"><i class="la la-search text-white"></i></span>
                                </span>
                                
                            </div>

                            <span nh-btn-action="locale-reload" class="btn btn-sm btn-outline-brand ml-2">
                                <i class="la la-refresh mr-0"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="kt-radio-inline kt-radio-inline-type-file">
                            <label class="kt-radio mb-0">
                                <input type="radio" name="type_category" nh-change-type data-type="category_product" checked="checked" /> 
                                    {__d('admin', 'san_pham')}
                                <span></span>
                            </label>
                            <label class="kt-radio mb-0">
                                <input type="radio" name="type_category" nh-change-type data-type="category_article"/> 
                                    {__d('admin', 'bai_viet')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div nh-form-table="table-locales-category" >			
        </div>
    </div>
</form>