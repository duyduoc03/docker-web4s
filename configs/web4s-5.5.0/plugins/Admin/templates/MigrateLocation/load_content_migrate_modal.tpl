<form id="merge-form" action="/admin/migrate-location/merge-data" method="POST" autocomplete="off">
    <div class="form-group">
        <div class="clearfix">
            <strong class="fs-16 text-danger">
                {if !empty($record_name)}
                    {if !empty($record_extend)}
                        {$record_extend}
                    {/if}
                    {$record_name}
                {/if}
            </strong>
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-20 mb-20"></div>

    <div class="form-group">
        <label>
            Hành động:
        </label>
        {assign var = list_type value = [
            'info' => 'Cập nhật thông tin',
            'merge' => 'Sáp nhập vào',
            'create' => 'Thành lập mới'
        ]}

        {$this->Form->select('type', $list_type, ['empty' => null, 'default' => $type_suggest, 'class' => 'form-control form-control-sm kt-selectpicker'])}
    </div>

    <div nh-wrap="update" class="kt-section mb-0 {if !empty($type_suggest) && $type_suggest == 'merge'}d-none{/if}">
        <span class="kt-section__info">
            Đồng bộ với :
        </span>
        <div class="kt-section__content kt-section__content--solid p-10">
            <div class="form-group">
                <label>
                    {if $object == 'city'}
                        Tỉnh thành
                    {/if}

                    {if $object == 'district'}
                        Quận huyện
                    {/if}

                    {if $object == 'ward'}
                        Phường xã
                    {/if}
                </label>

                {$this->Form->select('merge_record_id', $records_migrate, ['empty' => '-- Chọn --', 'default' => $migrate_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '6', 'data-live-search' => true])}
            </div>  
        </div>
    </div> 

    <div nh-wrap="merge" class="kt-section mb-0 {if empty($type_suggest) || $type_suggest != 'merge'}d-none{/if}">
        <span class="kt-section__info">
            Sáp nhập vào :
        </span>
        <div class="kt-section__content kt-section__content--solid p-10">
            {if !empty($cities)}
                <div class="form-group">
                    <label>
                        Thuộc tỉnh thành
                    </label>
                    {$this->Form->select('city_id', $cities, ['id' => 'city_id', 'empty' => '-- Chọn --', 'default' => $city_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '6', 'data-live-search' => true])}
                </div>
            {/if}

            {if !empty($districts)}
                <div class="form-group">
                    <label>
                        Thuộc quận huyện
                    </label>

                    {$this->Form->select('district_id', $districts, ['id' => 'district_id', 'empty' => '-- Chọn --', 'default' => $district_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '6', 'data-live-search' => true])}
                </div>
            {/if}

            {if !empty($wards)}
                <div class="form-group">
                    <label>
                        Thuộc phường xã
                    </label>

                    {$this->Form->select('ward_id', $wards, ['id' => 'ward_id', 'empty' => '-- Chọn --', 'default' => $ward_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '6', 'data-live-search' => true])}
                </div>
            {/if}
        </div>
    </div>    

    <input type="hidden" name="object" value="{$object}">
    <input type="hidden" name="record_id" value="{$record_id}">
</form>