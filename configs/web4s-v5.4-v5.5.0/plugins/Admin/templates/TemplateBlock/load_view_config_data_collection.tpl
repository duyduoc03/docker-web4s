<div class="row">
    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'truong_du_lieu')}
            </label>

            {$this->Form->select("collection_data_extend[collection_field]", $fields, ['empty' => "-- {__d('admin', 'chon')} -- ", 'default' => "{if !empty($collection_data_extend.collection_field)}{$collection_data_extend.collection_field}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
        </div>
    </div>

    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'dieu_kien')}
            </label>

            {assign var = conditions value = [
                'equal' => 'EQUAL',
                'gt' => 'GREATER_THAN',
                'lt' => 'LESS_THAN'
            ]}

            {$this->Form->select("collection_data_extend[collection_field_condition]", $conditions, ['empty' => null, 'default' => "{if !empty($collection_data_extend.collection_field)}{$collection_data_extend.collection_field}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
        </div>
    </div>

    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'gia_tri')}
            </label>
            <div nh-wrap="value-field-collection">
            	{if !empty($field_filter)}
            		{$field_filter['code'] = 'collection_data_extend[collection_field_value]'}
            		{if !empty($collection_data_extend.collection_field_value)}
            			{$field_filter['value'] = $collection_data_extend.collection_field_value}
            		{/if}
            	{/if}
            	{$this->element("../TemplateBlock/load_view_input_value_collection", [
            		'field_filter' => $field_filter
            	])}
            </div>            
        </div>
    </div>
</div>