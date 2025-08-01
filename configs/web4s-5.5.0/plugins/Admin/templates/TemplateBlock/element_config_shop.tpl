<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'sap_xep_theo')}
            </label>
            <div class="row">
                <div class="col-6">
                    {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfBrand(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>

                <div class="col-6">
                    {assign var = sort_type value = ''}
                    {if !empty($config[{SORT_TYPE}])}
                        {assign var = sort_type value = $config[{SORT_TYPE}]}
                    {/if}
                    <select name="config[{SORT_TYPE}]" class="form-control form-control-sm kt-selectpicker">
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

{assign var = data_type value = ''}
{if !empty($config[{DATA_TYPE}])}
    {assign var = data_type value = $config[{DATA_TYPE}]}
{/if}
<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'lay_du_lieu_theo')}
            </label>
            <select id="{DATA_TYPE}" name="config[{DATA_TYPE}]" class="form-control form-control-sm kt-selectpicker">
                <option value="">{__d('admin', 'tat_ca')}</option>
            </select>
        </div>
    </div>
</div>