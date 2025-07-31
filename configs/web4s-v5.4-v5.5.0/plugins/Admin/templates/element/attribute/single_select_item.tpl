{assign var = select_empty value = null}
{assign var = class_required value = ''}
{if !empty($required)}
	{assign var = class_required value = 'required'}
{else}
	{assign var = select_empty value = {__d('admin', 'chon_thuoc_tinh')}}	
{/if}

{assign var = live_search value = 0}
{if is_array($options) && count($options) > 5}
    {assign var = live_search value = 1}
{/if}

{$this->Form->select($code, $options, [
	'id' => $code, 
	'data-type' => 'special', 
	'empty' => {$select_empty}, 
	'default' => $value, 
	'class' => "form-control form-control-sm kt-selectpicker {$class_required} {$class}", 
	'disabled' => $disabled,
	'data-live-search' => $live_search
])}
