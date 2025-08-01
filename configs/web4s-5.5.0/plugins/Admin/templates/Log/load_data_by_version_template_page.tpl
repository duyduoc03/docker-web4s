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

		{assign var = layout_code_before value = "{if !empty($before_change.layout_code)}{$before_change.layout_code}{/if}"}
		{assign var = layout_code_after value = "{if !empty($after_change.layout_code)}{$after_change.layout_code}{/if}"}

		{if $layout_code_before != $layout_code_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'ma_layout')}
				</td>
				<td nh-log-field="before">
					{$layout_code_before}
				</td>
				<td nh-log-field="after">
					{$layout_code_after}
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

		{assign var = page_type_before value = "{if !empty($before_change.page_type)}{$before_change.page_type}{/if}"}
		{assign var = page_type_after value = "{if !empty($after_change.page_type)}{$after_change.page_type}{/if}"}
		
		{if $page_type_before != $page_type_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'loai_trang')}
				</td>
				<td nh-log-field="before">
					{$page_type_before}
				</td>
				<td nh-log-field="after">
					{$page_type_after}
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

		
		{assign var = TemplatesColumn_before value = []}
		{if !empty($before_change.TemplatesColumn)}
			{$TemplatesColumn_before = $this->TemplateAdmin->formatDataTemplates($before_change.TemplatesColumn, $lang_log)}
		{/if}
		{assign var = TemplatesColumn_after value = []}
		{if !empty($after_change.TemplatesColumn)}
			{$TemplatesColumn_after = $this->TemplateAdmin->formatDataTemplates($after_change.TemplatesColumn, $lang_log)}
		{/if}
	
		{if $TemplatesColumn_before != $TemplatesColumn_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'cau_hinh_cot')}
				</td>
				<td nh-log-field="before" class="json-content">
					
					{foreach from = $TemplatesColumn_before key = code item = value}
						{if !empty($value)}
							<p>	
								{$value}
							</p>
						{/if}
					{/foreach}
				</td>
				<td nh-log-field="after" class="json-content">
					
					{foreach from = $TemplatesColumn_after key = code item = value}
						{if !empty($value)}
							<p>
								{$value}
							</p>
						{/if}
					{/foreach}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = TemplatesRow_before value = []}
		{if !empty($before_change.TemplatesRow)}
			{$TemplatesRow_before = $this->TemplateAdmin->formatDataTemplates($before_change.TemplatesRow, $lang_log)}
		{/if}
		{assign var = TemplatesRow_after value = []}
		{if !empty($after_change.TemplatesRow)}
			{$TemplatesRow_after = $this->TemplateAdmin->formatDataTemplates($after_change.TemplatesRow, $lang_log)}
		{/if}
		
		{if $TemplatesRow_before != $TemplatesRow_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'cau_hinh_hang')}
				</td>
				<td nh-log-field="before" class="json-content">
					
					{foreach from = $TemplatesRow_before key = code item = value}
						{if !empty($value)}
							<p>	
								{$value}
							</p>
						{/if}
					{/foreach}
				</td>
				<td nh-log-field="after" class="json-content">
					
					{foreach from = $TemplatesRow_after key = code item = value}
						{if !empty($value)}
							<p>
								{$value}
							</p>
						{/if}
					{/foreach}
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
