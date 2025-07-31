<table class="table mb-0">
	<thead class="thead-light">
		<tr>
			<th class="col-md-2">
				{__d('admin', 'ten_truong')}
			</th>
			<th class="col-md-5">
				{__d('admin', 'truoc_cap_nhat')}
			</th>
			<th class="col-md-5">
				{__d('admin', 'sau_cap_nhat')}
			</th>
			<th class="col-md-1">
				
			</th>
		</tr>
	</thead>
	<tbody>

		{assign var = template_code_before value = "{if !empty($before_change.template_code)}{$before_change.template_code}{/if}"}
		{assign var = template_code_after value = "{if !empty($after_change.template_code)}{$after_change.template_code}{/if}"}

		{if $template_code_before != $template_code_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'ma_giao_dien_mau')}
				</td>
				<td nh-log-field="before">
					{$template_code_before}
				</td>
				<td nh-log-field="after">
					{$template_code_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = code_before value = "{if !empty($before_change.code)}{$before_change.code}{/if}"}
		{assign var = code_after value = "{if !empty($after_change.code)}{$after_change.code}{/if}"}

		{if $code_before != $code_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'ma_khoi')}
				</td>
				<td nh-log-field="before">
					{$code_before}
				</td>
				<td nh-log-field="after">
					{$code_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>

			</tr>
		{/if}

		{assign var = name_before value = "{if !empty($before_change.name)}{$before_change.name}{/if}"}
		{assign var = name_after value = "{if !empty($after_change.name)}{$after_change.name}{/if}"}
		
		{if $name_before != $name_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'ten')}
				</td>
				<td nh-log-field="before">
					{$name_before}
				</td>
				<td nh-log-field="after">
					{$name_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = type_before value = "{if !empty($before_change.type)}{$before_change.type}{/if}"}
		{assign var = type_after value = "{if !empty($after_change.type)}{$after_change.type}{/if}"}
		
		{if $type_before != $type_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'loai')}
				</td>
				<td nh-log-field="before">
					{$type_before}
				</td>
				<td nh-log-field="after">
					{$type_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = status_before value = "{if !empty($before_change.status)}{__d('admin', 'hoat_dong')}{else}{__d('admin', 'khong_hoat_dong')}{/if}"}
		{assign var = status_after value = "{if !empty($after_change.status)}{__d('admin', 'hoat_dong')}{else}{__d('admin', 'khong_hoat_dong')}{/if}"}
		
		{if $status_before != $status_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'trang_thai')}
				</td>
				<td nh-log-field="before">
					{$status_before}
				</td>
				<td nh-log-field="after">
					{$status_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}
		
		{assign var = view_before value = "{if !empty($before_change.view)}{$before_change.view}{/if}"}
		{assign var = view_after value = "{if !empty($after_change.view)}{$after_change.view}{/if}"}
		
		{if $view_before != $view_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'view')}
				</td>
				<td nh-log-field="before">
					{$view_before}
				</td>
				<td nh-log-field="after">
					{$view_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = data_extend_before value = "{if !empty($before_change.data_extend)}{$before_change.data_extend}{/if}"}
		{assign var = data_extend_after value = "{if !empty($after_change.data_extend)}{$after_change.data_extend}{/if}"}
		
		{if $data_extend_before != $data_extend_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'du_lieu_mo_rong')}
				</td>
				<td nh-log-field="before" class="json-content">
					<span>{$data_extend_before}</span>
				</td>
				<td nh-log-field="after" class="json-content">
					<span>{$data_extend_after}</span>
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = config_before value = []}
		{if !empty($before_change.config)}
			{$config_before = $this->TemplateBlockAdmin->formatDataConfig($before_change.config, $lang_log)}
		{/if}
		{assign var = config_after value = []}
		{if !empty($after_change.config)}
			{$config_after = $this->TemplateBlockAdmin->formatDataConfig($after_change.config, $lang_log)}
		{/if}
		
		{if $config_before != $config_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'cau_hinh')}
				</td>
				<td nh-log-field="before" class="json-content">
					{foreach from = $config_before key = code item = value}
						{if !empty($value)}
							<p>	
								<span><strong>{$code}:</strong> {$value}</span>
							</p>
						{/if}
					{/foreach}
				</td>
				<td nh-log-field="after" class="json-content">
					{foreach from = $config_after key = code item = value}
						{if !empty($value)}
							<p>
								<span><strong>{$code}:</strong> {$value}</span>
							</p>
						{/if}
					{/foreach}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = normal_data_extend_before value = "{if !empty($before_change.normal_data_extend)}{$before_change.normal_data_extend}{/if}"}
		{assign var = normal_data_extend_after value = "{if !empty($after_change.normal_data_extend)}{$after_change.normal_data_extend}{/if}"}
		
		{if $normal_data_extend_before != $normal_data_extend_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'du_lieu_mo_rong_thong_thuong')}
				</td>
				<td nh-log-field="before" class="json-content">
					<span>{$normal_data_extend_before}</span>
				</td>
				<td nh-log-field="after" class="json-content">
					<span>{$normal_data_extend_after}</span>
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = collection_data_extend_before value = "{if !empty($before_change.collection_data_extend)}{$before_change.collection_data_extend}{/if}"}
		{assign var = collection_data_extend_after value = "{if !empty($after_change.collection_data_extend)}{$after_change.collection_data_extend}{/if}"}
		
		{if $collection_data_extend_before != $collection_data_extend_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'bo_du_lieu_mo_rong')}
				</td>
				<td nh-log-field="before" class="json-content">
					<span>{$collection_data_extend_before}</span>
				</td>
				<td nh-log-field="after" class="json-content">
					<span>{$collection_data_extend_after}</span>
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}
	</tbody>
</table>
{if !empty($before_change)}
	<div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-0 mb-10"></div>

	<div class="form-group form-group-last text-right">
		<span nh-btn="rollback-log" version="{if !empty($version)}{$version}{/if}" record-id="{if !empty($record_id)}{$record_id}{/if}" class="btn btn-sm btn-danger">
			<i class="fa fa-undo"></i>
			{__d('admin', 'quay_lai_ban_ghi_cu')}
		</span>
	</div>
{/if}
