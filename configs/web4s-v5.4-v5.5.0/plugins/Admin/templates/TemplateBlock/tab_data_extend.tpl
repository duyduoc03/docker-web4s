{assign var = languages value = $this->LanguageAdmin->getList()}
{assign var = lang_default value = $this->LanguageAdmin->getDefaultLanguage()}

{assign var = normal_data_extend value = []}
{if !empty($block_info.normal_data_extend)}
    {$normal_data_extend = $block_info.normal_data_extend|json_decode:1}
{/if}

{assign var = collection_data_extend value = []}
{if !empty($block_info.collection_data_extend)}
    {$collection_data_extend = $block_info.collection_data_extend|json_decode:1}
{/if}

<div id="tab-data-extend" class="tab-pane" role="tabpanel">
    <form id="data-extend-form" action="{ADMIN_PATH}/template/block/save/data-extend{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        <div class="form-group">
            {if $supper_admin}
                <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
                    <i class="fa fa-images"></i>
                    {__d('admin', 'chon_anh_giao_dien')}
                </span>
                <input id="image_template" type="hidden" value="">
            {/if}
            
            {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

            <span btn-select-media-block="cdn" action="copy" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand">
                <i class="fa fa-photo-video"></i>
                {__d('admin', 'chon_anh_tu_cdn')}
            </span>
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-10"></div>

        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
            <li class="nav-item">
                <a href="#tab-normal-data-extend" class="nav-link active" data-toggle="tab" role="tab">
                    {__d('admin', 'nhan')}
                </a>
            </li>

            <li class="nav-item">
                <a href="#tab-collection-extend" class="nav-link" data-toggle="tab" role="tab">
                    Collection Data
                </a>
            </li>

            <li class="nav-item">
                <a href="#tab-json-data-extend" class="nav-link" data-toggle="tab" role="tab">
                    JSON Data
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="tab-normal-data-extend" class="tab-pane active" role="tabpanel">
                <div class="form-group">
                    <label class="lh-35px m-0">
                        {__d('admin', 'nhan_da_ngon_ngu')}
                        <i class="fs-11 text-muted">
                            (*{__d('admin', 'dung_de_hien_thi_cac_du_lieu_theo_ngon_ngu')})
                        </i>
                    </label>

                    <span id="btn-add-locale-label" class="float-right btn btn-success btn-sm mb-5">
                        <i class="fa fa-plus"></i>
                        {__d('admin', 'them_nhan_moi')}
                    </span>

                    <table id="table-locale-label" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-20">
                                    {__d('admin', 'ma')}
                                </th>

                                {if !empty($languages)}
                                    {foreach from = $languages key = lang item = language}
                                        <th>
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="h-15px w-15px rounded mr-5"/>
                                            {$language}
                                        </th>
                                    {/foreach}
                                {/if}

                                <th class="w-3 pr-0 text-center">
                                    <i class="fa fa-cog"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {* Nếu không có dữ liệu locale thì set value của nó bằng 1 mảng mặc định để không phải xử lý riêng với trường hợp nó rỗng *}
                            {if empty($normal_data_extend.locale)}
                                {$normal_data_extend.locale = ['vi' => ['' => '']]}
                            {/if}

                            {$first_language = $normal_data_extend.locale|reset}

                            {foreach from = $first_language key = key item = item}
                                <tr>
                                    <td class="pl-0 pr-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-barcode"></i>
                                                </span>
                                            </div>
                                            <input value="{if !empty($key)}{$key}{/if}" {if empty($addons[{INTERFACE_EDIT}])}disabled{/if} nh-input="key" type="text" class="form-control form-control-sm fs-12 text-danger">
                                        </div>
                                    </td>

                                    {if !empty($languages)}
                                        {foreach from = $languages key = lang item = language}
                                            {assign var = value value = ''}
                                            {if !empty($normal_data_extend.locale[$lang][$key])}
                                                {$value = $normal_data_extend.locale[$lang][$key]}
                                            {/if}
                                            <td class="pr-0">
                                                <div class="input-group">                                                            
                                                    <textarea nh-input="value" nh-language="{$lang}" class="form-control form-control-sm fs-12" rows="1" placeholder="{$language}" style="min-height: 33px;">{if !empty($value)}{$value}{/if}</textarea>
                                                    <div class="input-group-append">
                                                        {if $lang == $lang_default}
                                                            <span nh-btn="data-extend-translate" nh-language-default="{$lang_default}" class="input-group-text cursor-p" title="{__d('admin', 'dich')}">
                                                                <i class="fa fa-language kt-font-brand"></i>
                                                            </span>
                                                        {/if}
                                                    </div>
                                                </div>
                                                
                                            </td>
                                        {/foreach}
                                    {/if}
                                    
                                    <td class="pr-0 text-center">
                                        <i nh-delete="data-extend" class="fa fa-trash-alt btn btn-secondary btn-sm"></i>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-collection-extend" class="tab-pane" role="tabpanel">
                <div class="row">
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'so_luong_ban_ghi')}
                            </label>            
                            <input name="collection_data_extend[number_record]" value="{if !empty($collection_data_extend.number_record)}{$collection_data_extend.number_record}{/if}" class="form-control form-control-sm" type="number">
                            <span class="form-text text-muted">
                                {__d('admin', 'gioi_han_{0}_ban_ghi', [200])}
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'sap_xep_theo')}
                            </label>
                            <div class="row">
                                <div class="col-6">
                                    {assign var = sort_fields value = [
                                        'created' => __d('admin', 'ngay_tao'), 
                                        'position' => __d('admin', 'vi_tri')
                                    ]}
                                    {$this->Form->select("collection_data_extend[sort_field]", $sort_fields, ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($collection_data_extend.sort_field)}{$collection_data_extend.sort_field}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>

                                <div class="col-6">
                                    {assign var = sort_type value = ''}
                                    {if !empty($collection_data_extend.sort_type)}
                                        {assign var = sort_type value = $collection_data_extend.sort_type}
                                    {/if}
                                    <select name="collection_data_extend[sort_type]" class="form-control form-control-sm kt-selectpicker">
                                        <option value="{DESC}" {if $sort_type == {DESC}}selected="true"{/if}>
                                            {__d('admin', 'giam_dan')}
                                        </option>

                                        <option value="{ASC}" {if $sort_type == {ASC}}selected="true"{/if}>
                                            {__d('admin', 'tang_dan')}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chon_du_lieu_mo_rong')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {assign var = collections value = $this->ExtendCollectionAdmin->getListActived()}
                            {$collections = $this->UtilitiesAdmin->hashCombineData($collections, 'code', 'name')}
                            
                            {assign var = extend_collection value = "{if !empty($collection_data_extend.extend_collection)}{$collection_data_extend.extend_collection}{/if}"}

                            {$this->Form->select("collection_data_extend.extend_collection", $collections, ['empty' => "-- {__d('admin', 'chon')} --", 'default' => "{$extend_collection}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>

                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'du_lieu')}
                            </label>

                            {assign var = data_type value = [
                                'all' => "{__d('admin', 'tat_ca')}",
                                'filter_by_field' => "{__d('admin', 'loc_theo_truong')}"
                            ]}

                            {assign var = get_data_type value = "{if !empty($collection_data_extend.get_data_type)}{$collection_data_extend.get_data_type}{/if}"}

                            {$this->Form->select("collection_data_extend[get_data_type]", $data_type, ['empty' => null, 'default' => $get_data_type, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
                <span class="form-text text-muted mb-10">
                    <a href="javascript:;" data-toggle="modal" data-target="#data-collection-example-modal">
                        {__d('admin', 'du_lieu_mau')} <i class="fa fa-angle-double-right fs-11"></i>
                    </a>
                </span>
                <div nh-wrap="config-data-collection" class="clearfix"></div>                
            </div>

            <div id="tab-json-data-extend" class="tab-pane" role="tabpanel">
                <span class="form-text text-muted mb-10">
                    Dữ liệu mở rộng sẽ được lưu dưới dạng <a href="https://www.w3schools.com/whatis/whatis_json.asp" target="_blank">JSON</a> và block có thể đọc được dữ liệu này để hiển thị ra ngoài.
                    <a href="javascript:;" data-toggle="modal" data-target="#data-example-modal">
                        {__d('admin', 'du_lieu_mau')}
                    </a>
                </span>

                <div id="editor-data-extend" class="nh-editor"></div>  
            </div>
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-10"></div>

        <br class="mb-20">

        <input id="input-data-extend" name="data_extend" value="{if !empty($block_info.data_extend)}{htmlentities($block_info.data_extend)}{/if}" type="hidden">
        <input id="input-normal-data-extend" name="normal_data_extend" value="{if !empty($block_info.data_extend)}{htmlentities($block_info.data_extend)}{/if}" type="hidden">

        <div class="form-group mb-0">
            <div class="btn-group">
                <span class="btn btn-sm btn-brand btn-save">
                    {__d('admin', 'luu_du_lieu')}
                </span>
            </div>
        </div>
    </form>
</div>