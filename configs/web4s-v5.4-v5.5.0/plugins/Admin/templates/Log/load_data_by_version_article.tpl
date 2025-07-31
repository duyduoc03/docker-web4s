{assign var = mutiple_language_before value = []}
{if !empty($before_change['mutiple_language'])}
	{$mutiple_language_before = $before_change['mutiple_language']}
{/if}
{assign var = mutiple_language_after value = []}
{if !empty($after_change['mutiple_language'])}
	{$mutiple_language_after = $after_change['mutiple_language']}
{/if}

{assign var = mutiple_language_all value = []}
{if !empty($mutiple_language_before) || !empty($mutiple_language_after)}
	{$mutiple_language_all = $this->AttributeAdmin->formatDataMutipleLanguage($mutiple_language_before ,$mutiple_language_after )}
{/if}

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

	{foreach from = $mutiple_language_all key = lang item = item_mutiple}
		{assign var = name_before value = ''}
		{if !empty($item_mutiple['name']['before'])}
			{$name_before = $item_mutiple['name']['before']}
		{/if}
		{assign var = name_after value = ''}
		{if !empty($item_mutiple['name']['after'])}
			{$name_after = $item_mutiple['name']['after']}
		{/if}
		
		{if $name_before != $name_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'tieu_de')} 
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
		

		{assign var = description_before value = ''}
		{if !empty($item_mutiple['description']['before'])}
			{$description_before = $item_mutiple['description']['before']}
		{/if}
		{assign var = description_after value = ''}
		{if !empty($item_mutiple['description']['after'])}
			{$description_after = $item_mutiple['description']['after']}
		{/if}
		
		{if $description_before != $description_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'mo_ta')}
				</td>
				<td nh-log-field="before" class="json-content">
					{htmlentities($description_before|truncate:300:" ...")}
				</td>
				<td nh-log-field="after" class="json-content">
					{htmlentities($description_after|truncate:300:" ...")}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}
		
		{assign var = content_before value = ''}
		{if !empty($item_mutiple['content']['before'])}
			{$content_before = $item_mutiple['content']['before']}
		{/if}
		{assign var = content_after value = ''}
		{if !empty($item_mutiple['content']['after'])}
			{$content_after = $item_mutiple['content']['after']}
		{/if}
		
		{if $content_before != $content_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'noi_dung')}
				</td>
				<td nh-log-field="before" class="json-content">
					{htmlentities($content_before|truncate:300:" ...")}
				</td>
				<td nh-log-field="after" class="json-content">
					{htmlentities($content_after|truncate:300:" ...")}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = seo_title_before value = ''}
		{if !empty($item_mutiple['seo_title']['before'])}
			{$seo_title_before = $item_mutiple['seo_title']['before']}
		{/if}
		{assign var = seo_title_after value = ''}
		{if !empty($item_mutiple['seo_title']['after'])}
			{$seo_title_after = $item_mutiple['seo_title']['after']}
		{/if}
		
		{if $seo_title_before != $seo_title_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'tieu_de_seo')}
				</td>
				<td nh-log-field="before">
					{$seo_title_before}
				</td>
				<td nh-log-field="after">
					{$seo_title_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = seo_description_before value = ''}
		{if !empty($item_mutiple['seo_description']['before'])}
			{$seo_description_before = $item_mutiple['seo_description']['before']}
		{/if}
		{assign var = seo_description_after value = ''}
		{if !empty($item_mutiple['seo_description']['after'])}
			{$seo_description_after = $item_mutiple['seo_description']['after']}
		{/if}
		
		{if $seo_description_before != $seo_description_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'mo_ta_seo')}
				</td>
				<td nh-log-field="before">
					{$seo_description_before|strip_tags|truncate:100:" ..."}
				</td>
				<td nh-log-field="after">
					{$seo_description_after|strip_tags|truncate:100:" ..."}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{***seo_keyword*}
		{assign var = seo_keyword_before value = ''}
		{if !empty($item_mutiple['seo_keyword']['before'])}
			{$seo_keyword_before = $item_mutiple['seo_keyword']['before']}
		{/if}
		{assign var = seo_keyword_after value = ''}
		{if !empty($item_mutiple['seo_keyword']['after'])}
			{$seo_keyword_after = $item_mutiple['seo_keyword']['after']}
		{/if}


		{if $seo_keyword_before != $seo_keyword_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'tu_khoa_seo')}
				</td>
				<td nh-log-field="before">
					{$seo_keyword_before}
				</td>
				<td nh-log-field="after">
					{$seo_keyword_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = url_before value = ''}
		{if !empty($item_mutiple['url']['before'])}
			{$url_before = $item_mutiple['url']['before']}
		{/if}
		{assign var = url_after value = ''}
		{if !empty($item_mutiple['url']['after'])}
			{$url_after = $item_mutiple['url']['after']}
		{/if}
		
		{if $url_before != $url_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'duong_dan')}
				</td>
				<td nh-log-field="before">
					{$url_before}
				</td>
				<td nh-log-field="after">
					{$url_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}
	{/foreach}
	
		{assign var = tags_before value = []}
		{if !empty($before_change.tags)}
			{$tags_before = $before_change.tags}
		{/if}
		{assign var = tags_after value = []}
		{if !empty($after_change.tags)}
			{$tags_after = $after_change.tags}
		{/if}

		{if $tags_before != $tags_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'the_bai_viet')}
				</td>
				<td nh-log-field="before">
					{foreach from = $tags_before item = tags_value}
						{if !empty($tags_value)}
							{$tags_value = $tags_value|json_decode:true}
						{/if}
							<p>
								{$tags_value['name']}
							</p>
					 {/foreach}
				</td>
				<td nh-log-field="after">
					{foreach from = $tags_after item = tags_value}
						{if !empty($tags_value)}
							{$tags_value = $tags_value|json_decode:true}
						{/if}
							<p>
								{$tags_value['name']}
							</p>
					 {/foreach}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = categories_before value = ''}
		{if !empty($before_change.categories)}
			{$categories = $this->UtilitiesAdmin->hashExtractData($before_change.categories, 'name')}
			{$categories_before = ', '|implode:$categories}
		{/if}

		{assign var = categories_after value = ''}
		{if !empty($after_change.categories)}
			{$categories = $this->UtilitiesAdmin->hashExtractData($after_change.categories, 'name')}
			{$categories_after = ', '|implode:$categories}
		{/if}

		{if $categories_before != $categories_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'danh_muc')}
				</td>
				<td nh-log-field="before">
					{$categories_before}
				</td>
				<td nh-log-field="after">
					{$categories_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = main_category_before value = ''}
		{if !empty($before_change.main_category_id)}
			{$main_category_before = $this->CategoryAdmin->getAllNameContent($before_change.main_category_id)}
		{/if}
		{assign var = main_category_after value = ''}
		{if !empty($after_change.main_category_id)}
			{$main_category_after = $this->CategoryAdmin->getAllNameContent($after_change.main_category_id)}
		{/if}

		{if $main_category_before != $main_category_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'danh_muc_chinh')}
				</td>
				
				<td nh-log-field="before">
					{if !empty($main_category_before)}
						{$main_category_before[$lang_log]}
					{/if}
				</td>
				<td nh-log-field="after">
					{if !empty($main_category_after)}
						{$main_category_after[$lang_log]}
					{/if}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = position_before value = "{if !empty($before_change.position)}{$before_change.position}{/if}"}
		{assign var = position_after value = "{if !empty($after_change.position)}{$after_change.position}{/if}"}
		
		{if $position_before != $position_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'vi_tri')}
				</td>
				<td nh-log-field="before">
					{$position_before}
				</td>
				<td nh-log-field="after">
					{$position_after}
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
					{__d('admin', 'luot_xem')}
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

		{assign var = featured_before value = "{if !empty($before_change.featured)}{__d('admin', 'noi_bat')}{else}{__d('admin', 'khong_noi_bat')}{/if}"}
		{assign var = featured_after value = "{if !empty($after_change.featured)}{__d('admin', 'noi_bat')}{else}{__d('admin', 'khong_noi_bat')}{/if}"}
		
		{if $featured_before != $featured_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'noi_bat')}
				</td>
				<td nh-log-field="before">
					{$featured_before}
				</td>
				<td nh-log-field="after">
					{$featured_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = catalogue_before value = "{if !empty($before_change.catalogue)}{__d('admin', 'co')}{else}{__d('admin', 'khong')}{/if}"}
		{assign var = catalogue_after value = "{if !empty($after_change.catalogue)}{__d('admin', 'co')}{else}{__d('admin', 'khong')}{/if}"}
		
		{if $featured_before != $featured_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'hien_thi_muc_luc')}
				</td>
				<td>
					{$catalogue_before}
				</td>
				<td>
					{$catalogue_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = url_video_before value = "{if !empty($before_change.url_video)}{$before_change.url_video}{/if}"}
		{assign var = url_video_after value = "{if !empty($after_change.url_video)}{$after_change.url_video}{/if}"}
		
		{if $url_video_before != $url_video_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'duong_dan_video')}
				</td>
				<td nh-log-field="before">
					{$url_video_before}
				</td>
				<td nh-log-field="after">
					{$url_video_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = type_video_before value = "{if !empty($before_change.type_video)}{$before_change.type_video}{/if}"}
		{assign var = type_video_after value = "{if !empty($after_change.type_video)}{$after_change.type_video}{/if}"}
		
		{if $type_video_before != $type_video_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'loai_video')}
				</td>
				<td nh-log-field="before">
					{if $type_video_before == 'video_youtube'}
						<i class="flaticon-youtube fs-20 text-primary"></i>
					{else}
						<span class="fa fa-video text-primary"></span>
					{/if}
				</td>
				<td nh-log-field="after">
					{if $type_video_after == 'video_youtube'}
							<i class="flaticon-youtube fs-20 text-primary"></i>
					{else}
							<span class="fa fa-video text-primary"></span>
					{/if}
				</td>
			</tr>
		{/if}

		
		{assign var = files_before value = []}
		{if !empty($before_change.files)}
			{$files_before = $before_change.files}
		{/if}
		{assign var = files_after value = []}
		{if !empty($after_change.files)}
			{$files_after = $after_change.files}
		{/if}

		{if $files_before != $files_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'tep')}
				</td>
				<td nh-log-field="before">
					 {foreach from = $files_before item = file}
						<p>{CDN_URL}{$file}</p>
					 {/foreach}
				</td>
				<td nh-log-field="after">
					{foreach from = $files_after item = file}
						<p>{CDN_URL}{$file}</p>
					 {/foreach}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = images_before value = []}
		{if !empty($before_change.images)}
			{$images_before = $before_change.images}
		{/if}
		{assign var = images_after value = []}
		{if !empty($after_change.images)}
			{$images_after = $after_change.images}
		{/if}
		
		{if $images_before != $images_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'album')}
				</td>
				<td nh-log-field="before">
					 {foreach from = $images_before item = image_item}
						<p>{CDN_URL}{$image_item}</p>
					 {/foreach}
				</td>
				<td nh-log-field="after">
					{foreach from = $images_after item = image_item}
						<p>{CDN_URL}{$image_item}</p>
					 {/foreach}
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

		{assign var = image_avatar_before value = "{if !empty($before_change.image_avatar)}{CDN_URL}{$before_change.image_avatar}{/if}"}
		{assign var = image_avatar_after value = "{if !empty($after_change.image_avatar)}{CDN_URL}{$after_change.image_avatar}{/if}"}
		
		{if $image_avatar_before != $image_avatar_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'anh_chinh')}
				</td>
				<td nh-log-field="before">
					{$image_avatar_before}
				</td>
				<td nh-log-field="after">
					{$image_avatar_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = author_before value = "{if !empty($before_change.author_id)}{$before_change.author_id}{/if}"}
		{assign var = author_after value = "{if !empty($after_change.author_id)}{$after_change.author_id}{/if}"}

		{if $author_before != $author_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'tac_gia')}
				</td>
				
				<td nh-log-field="before">
					{if !empty($author_before)}
						{$author_before}
					{/if}
				</td>
				<td nh-log-field="after">
					{if !empty($author_after)}
						{$author_after}
					{/if}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = attributes_before value = []}
		{if !empty($before_change.attributes)}
			{$attributes_before = $this->AttributeAdmin->formatDataAttributes($before_change.attributes , $lang_log, 'article')}
		{/if}

		{assign var = attributes_after value = []}
		{if !empty($after_change.attributes)}
			{$attributes_after = $this->AttributeAdmin->formatDataAttributes($after_change.attributes , $lang_log, 'article')}
		{/if}

		{assign var = attributes_all value = []}
		{if !empty($attributes_before) || !empty($attributes_after)}
			{$attributes_all = $this->AttributeAdmin->formatDataAttributesAll($attributes_before ,$attributes_after )}
		{/if}

		{if $attributes_before != $attributes_after}
			{foreach from = $attributes_all key = code item = attribute}
				<tr>	
					{if $attribute@first}
						<td class="kt-font-bolder" rowspan="{count($attributes_all)}">
							{__d('admin', 'thuoc_tinh_mo_rong')} 
						</td>
					{/if}

					{if $attribute['before']['value'] != $attribute['after']['value']}	
						<td class="json-content">
							{if !empty($attribute['before']['name'])}
						 			<strong>
							 			{$attribute['before']['name']}
							 		</strong>							 	
						 	{/if}
		
							{if !empty($attribute['before']['value'])}
							 	{foreach from = $attribute['before']['value'] key = code item = value_before}
							 		<p nh-log-field="before"> 
							 			{$value_before|truncate:400:" ..."}
							 		</p>
							 	{/foreach}
							{/if}
						</td>
					{/if}

					{if $attribute['before']['value'] != $attribute['after']['value']}	
						<td  class="json-content">
							{if !empty($attribute['after']['name'])}
									<strong class="d-block">
										{$attribute['after']['name']}
									</strong>
								
						 	{/if}
						 	
							{if !empty($attribute['after']['value'])}
							 	{foreach from = $attribute['after']['value'] key = code item = value_after}
							 		<p nh-log-field="after">
							 			{$value_after|truncate:400:" ..."}
							 		</p>
							 	{/foreach}
							{/if}
						</td>
						<td>
							<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
						</td>
					{/if}
				</tr>
			{/foreach}	
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
