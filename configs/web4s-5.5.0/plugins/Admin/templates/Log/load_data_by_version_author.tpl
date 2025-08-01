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
{* {$before_change|debug}
{$after_change|debug} *}

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
		{assign var = job_title_before value = ''}
		{if !empty($item_mutiple['job_title']['before'])}
			{$job_title_before = $item_mutiple['job_title']['before']}
		{/if}
		{assign var = job_title_after value = ''}
		{if !empty($item_mutiple['job_title']['after'])}
			{$job_title_after = $item_mutiple['job_title']['after']}
		{/if}
		
		{if $job_title_before != $job_title_after}
			<tr>
				<td class="kt-font-bolder">
					<img src="/admin/assets/media/flags/{$lang}.svg" style="width: 20px; height: 15px;"> {__d('admin', 'chuc_vu')} 
				</td>
				<td nh-log-field="before">
					{$job_title_before}
				</td>
				<td nh-log-field="after">
					{$job_title_after}
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

		{assign var = social_before value = []}
		{if !empty($before_change.social)}
			{$social_before = $before_change.social}
		{/if}
		{assign var = social_after value = []}
		{if !empty($after_change.social)}
			{$social_after = $after_change.social}
		{/if}

		{if $social_before != $social_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'mang_xa_hoi')}
				</td>
				<td nh-log-field="before">
					{if !empty($social_before['facebook'])}
						<p>
							facebook: {$social_before['facebook']}
						</p>
					{/if}
					{if !empty($social_before['instagram'])}
						<p>
							instagram: {$social_before['instagram']}
						</p>
					{/if}
					{if !empty($social_before['youtube'])}
						<p>
							youtube: {$social_before['youtube']}
						</p>
					{/if}
					{if !empty($social_before['tiktok'])}
						<p>
							tiktok: {$social_before['tiktok']}
						</p>
					{/if}
					{if !empty($social_before['twitter'])}
						<p>
							twitter: {$social_before['twitter']}
						</p>
					{/if}
					{if !empty($social_before['linkedin'])}
						<p>
							linkedin: {$social_before['linkedin']}
						</p>
					{/if}
					{if !empty($social_before['others'])} 
						{foreach from = $social_before['others'] key = key item = item}
							<p>
								{if !empty($item.name)}
									{$item.name}: 
								{/if}
								{if !empty($item.url)}
									{$item.url} 
								{/if}
							</p>
						{/foreach}
					{/if}

				</td>
				<td nh-log-field="after">
					{if !empty($social_after['facebook'])}
						<p>
							facebook: {$social_after['facebook']}
						</p>
					{/if}
					{if !empty($social_after['instagram'])}
						<p>
							instagram: {$social_after['instagram']}
						</p>
					{/if}
					{if !empty($social_after['youtube'])}
						<p>
							youtube: {$social_after['youtube']}
						</p>
					{/if}
					{if !empty($social_after['tiktok'])}
						<p>
							tiktok: {$social_after['tiktok']}
						</p>
					{/if}
					{if !empty($social_after['twitter'])}
						<p>
							twitter: {$social_after['twitter']}
						</p>
					{/if}
					{if !empty($social_after['linkedin'])}
						<p>
							linkedin: {$social_after['linkedin']}
						</p>
					{/if}
					{if !empty($social_after['others'])} 
						{foreach from = $social_after['others'] key = key item = item}
							<p>
								{if !empty($item.name)}
									{$item.name}: 
								{/if}
								{if !empty($item.url)}
									{$item.url} 
								{/if}
							</p>
						{/foreach}
					{/if}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

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

		{assign var = full_name_before value = "{if !empty($before_change.full_name)}{$before_change.full_name}{/if}"}
		{assign var = full_name_after value = "{if !empty($after_change.full_name)}{$after_change.full_name}{/if}"}
		
		{if $full_name_before != $full_name_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'ho_va_ten')}
				</td>
				<td nh-log-field="before">
					{$full_name_before}
				</td>
				<td nh-log-field="after">
					{$full_name_after}
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

		{assign var = email_before value = "{if !empty($before_change.email)}{$before_change.email}{/if}"}
		{assign var = email_after value = "{if !empty($after_change.email)}{$after_change.email}{/if}"}
		
		{if $email_before != $email_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'Email')}
				</td>
				<td nh-log-field="before">
					{$email_before}
				</td>
				<td nh-log-field="after">
					{$email_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = phone_before value = "{if !empty($before_change.phone)}{$before_change.phone}{/if}"}
		{assign var = phone_after value = "{if !empty($after_change.phone)}{$after_change.phone}{/if}"}
		
		{if $phone_before != $phone_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'so_dien_thoai')}
				</td>
				<td nh-log-field="before">
					{$phone_before}
				</td>
				<td nh-log-field="after">
					{$phone_after}
				</td>
				<td>
					<i nh-btn="show-change-field" class="fa fa-exchange-alt fa cursor-p fs-14 text-primary"></i>
				</td>
			</tr>
		{/if}

		{assign var = address_before value = "{if !empty($before_change.address)}{$before_change.address}{/if}"}
		{assign var = address_after value = "{if !empty($after_change.address)}{$after_change.address}{/if}"}
		
		{if $address_before != $address_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'duong_dan_video')}
				</td>
				<td nh-log-field="before">
					{$address_before}
				</td>
				<td nh-log-field="after">
					{$address_after}
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

		{assign var = avatar_before value = "{if !empty($before_change.avatar)}{CDN_URL}{$before_change.avatar}{/if}"}
		{assign var = avatar_after value = "{if !empty($after_change.avatar)}{CDN_URL}{$after_change.avatar}{/if}"}
		
		{if $avatar_before != $avatar_after}
			<tr>
				<td class="kt-font-bolder">
					{__d('admin', 'anh_chinh')}
				</td>
				<td nh-log-field="before">
					{$avatar_before}
				</td>
				<td nh-log-field="after">
					{$avatar_after}
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