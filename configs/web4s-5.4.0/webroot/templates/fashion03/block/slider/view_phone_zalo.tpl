{strip}
{if !empty($data_block)}
    <div class="contact-right">
        {foreach from = $data_block item = slider key = k_slider name = each_slider}			
			{assign var = image_source value = ''}
			{if !empty($slider.image) && !empty($slider.image_source)}
				{assign var = image_source value = $slider.image_source}
			{/if}

			{assign var = image_url value = ''}
			{if !empty($slider.image) && $image_source == 'cdn'}
				{assign var = image_url value = "{CDN_URL}{$slider.image}"}
			{/if}

			{if !empty($slider.image) && $image_source == 'template'}
				{assign var = image_url value = "{$slider.image}"}
			{/if}
			
			{assign var = url value = '/'}
			{if !empty($slider.url)}
				{assign var = url value = $this->Utilities->checkInternalUrl($slider.url)}
			{/if}
            <li class="entry-content {if !empty($slider.class_item)}{$slider.class_item}{/if}">
                <a href="{if !empty($slider.url)}{$slider.url}{else}#{/if}" title="{if !empty($slider.name)}{$slider.name}{/if}" {if !empty($slider.blank_link)}target="_blank"{/if}>
                    <img src="{$image_url}" alt="{if !empty($slider.name)}{$slider.name}{/if}">
                </a>
            </li>
		{/foreach}
    </div>
{/if}
{/strip}