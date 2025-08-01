{strip}
{if !empty($data_block)}
	<ul class="footer_menu">
		{foreach from = $data_block item = menu}
			{assign var = class_item value = ""}
			{if !empty($menu.class_item)}
				{assign var = class_item value = $menu.class_item}
			{/if}

			<li class="{if !$menu@last}mr-5{/if} {$class_item}">
				<a class="link_title hv_40 text-white text-uppercase" href="{if !empty($menu.url)}{$this->Utilities->checkInternalUrl($menu.url)}{else}/{/if}"
					{if !empty($menu.blank_link)}target="_blank"{/if}>
					{$menu.name|escape|truncate:60:" ..."}
				</a>
			</li>
		{/foreach}
	</ul>
{/if}
{/strip}