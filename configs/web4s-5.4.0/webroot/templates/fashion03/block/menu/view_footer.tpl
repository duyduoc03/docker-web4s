{strip}
<div class="footer-menu-section">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <div class="title-footer text-uppercase mb-4">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </div>
    {/if}
    {if !empty($data_block)}
		<ul class="list-unstyled">
			{foreach from = $data_block item = menu}
				{assign var = class_has_child value = ""}
				{if !empty($menu.has_sub_menu)}
					{assign var = class_has_child value = "has-child "}
				{/if}

				{assign var = class_position value = ""}
				{if !empty($menu.view_item) && $menu.view_item == 'sub_dropdown'}
					{assign var = class_position value = "position-relative "}
				{/if}

				{assign var = class_item value = ""}
				{if !empty($menu.class_item)}
					{assign var = class_item value = $menu.class_item}
				{/if}
				
				{if !empty($menu.name)}
					<li class="{$class_position}{$class_has_child}{$class_item}">
						<a href="{if !empty($menu.url)}{$this->Utilities->checkInternalUrl($menu.url)}{else}/{/if}" {if !empty($menu.blank_link)}target="_blank"{/if}>
							{$menu.name|escape|truncate:60:" ..."}
						</a>
					</li>
				{/if}
			{/foreach}
		</ul>
	{/if}
</div>
{/strip}