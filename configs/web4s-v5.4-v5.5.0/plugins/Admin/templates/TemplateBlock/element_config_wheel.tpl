{assign var = data_type value = 'wheel'}
{if !empty($config[{DATA_TYPE}])}
    {assign var = data_type value = $config[{DATA_TYPE}]}
{/if}

{assign var = data value = []}
{if !empty($config.data_ids)}
    {assign var = data value = $config.data_ids}
{/if}

<div id="wrap-view-data">
    {if !empty($data_type)}
        {$this->element("../TemplateBlock/load_view_data", ['data_type' => $data_type, 'block_type' => $type, 'data' => $data])}
    {/if}
</div>