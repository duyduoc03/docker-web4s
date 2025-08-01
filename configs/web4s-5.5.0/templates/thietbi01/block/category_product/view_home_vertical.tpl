{strip}
{assign var = display_menu value = 'style="display: block"'}
{if empty({DEVICE})}
	{assign var = display_menu value = 'style="display: none"'}
{/if}

{$menu_id = "menu-{time()}-{rand(1, 1000)}"}
<div class="menu-container menu-vertical">
	<a class="menu-vertical--title" href="javascript:;" nh-toggle="{$menu_id}">
		<i class="fa-light fa-bars"></i>
		{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
		    <h4 class="vertical_title">{$this->Block->getLocale('tieu_de', $data_extend)}</h4>
		{/if}
		<i class="fa-light fa-angle-down"></i>
	</a>
	<div class="back-drop"></div>
	<nav class="menu-vertical--nav" nh-menu="sidebar" menu-type="vertical">
		<div class="menu-vertical--top">
			<span class="menu-vertical--header">
				{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
				    {$this->Block->getLocale('tieu_de', $data_extend)}
				{/if}
			</span>
			<a href="javascript:;" nh-menu="btn-close" class="close-sidebar effect-rotate icon-close">
				<i class="fa-light fa-xmark"></i>
			</a>
		</div>

		{if !empty($data_block)}
			<ul class="menu-vertical--content list-unstyled mb-0" nh-toggle-element="{$menu_id}" {$display_menu}>
				{foreach from = $data_block.data item = menu}
					{if !empty($menu.name)}
						<li class="">
							<a href="{if !empty($menu.url)}{$this->Utilities->checkInternalUrl($menu.url)}{else}/{/if}">
								{$menu.name|escape|truncate:60:" ..."}
							</a>
						</li>
					{/if}
				{/foreach}
				{if !empty($data_extend['locale'][{LANGUAGE}]['link_tat_ca'])}
				    <div class="text-right">
	            	    <a class="menu-vertical--link-all" href="{$this->Block->getLocale('link_tat_ca', $data_extend)}">
	            	        {if !empty($data_extend['locale'][{LANGUAGE}]['label_link_tat_ca'])}
	            	            {$this->Block->getLocale('label_link_tat_ca', $data_extend)}
	            	            <i class="fa-light fa-arrow-right ml-3"></i>
	            	        {/if}
	            	    </a>
	        	    </div>
	        	{/if}
			</ul>
		{/if}
	</nav>
</div>
{/strip}