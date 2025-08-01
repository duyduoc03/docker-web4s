{if !empty($data_extend['locale'][{LANGUAGE}]['column_number']) && {$data_extend['locale'][{LANGUAGE}]['column_number']} < 6}
    {assign var = column_number value = {$this->Block->getLocale('column_number', $data_extend)}}
{else}
    {assign var = column_number value = 5}
{/if}
{assign var = column_custom value = $column_number - 1}
{assign var = column_flex value = (100/$column_number)+5}

{strip}
<ul nh-toggle-element="{$parent_menu_code}" class="entry-menu full-width scrollbar">
	<li class="container-menu container">
		{$data_sub_menu = array_chunk($data_sub_menu, $column_custom)}
		<ul class="row-menu mega_custom">
	        <li style="flex: 0 0 {100 - $column_flex}%; max-width: {100 - $column_flex}%;">
    		    {foreach from = $data_sub_menu key = k_0 item = item}
        			<ul class="row-menu">
        				{foreach from = $item key = k_1 item = sub_menu}
            	            <li class="column-{$column_custom} {if !empty($sub_menu.children)}has-child{/if}">
            	            	{if !empty($sub_menu.image_custom)}
            	            		<img src="{$this->Utilities->replaceVariableSystem($sub_menu.image_custom)}" alt="{if !empty($sub_menu.name)}{$sub_menu.name}{/if}" class="img-fluid pb-4 pt-4" />
            	            	{/if}
            
            	            	{assign var = class_item value = ""}
            					{if !empty($sub_menu.class_item)}
            						{assign var = class_item value = "<i class='{$sub_menu.class_item}'></i>"}
            					{/if} 
            					{if !empty($sub_menu.name)}
            						<a class="menu-title" href="javascrip;">
            							{$class_item}{$sub_menu.name|escape|truncate:60:" ..."}
            						</a>
            					{/if}
            					{if !empty($sub_menu.children)}
            						{assign var = class_item_children value = ""}
            						{if !empty($sub_menu.children.class_item_children)}
            							{assign var = class_item_children value = "<i class='{$sub_menu.children.class_item}'></i>"}
            						{/if}
            						<span class="grower" nh-toggle="{$parent_menu_code}-{$k_0}-{$k_1}"></span>
            						<ul nh-toggle-element="{$parent_menu_code}-{$k_0}-{$k_1}" class="sub-menu">
            							{foreach from = $sub_menu.children item = sub_sub_menu}
            								<li class="navigation__item">
            									<a class="menu-link navigation__link hv_40" href="{if !empty($sub_sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_sub_menu.url)}{else}/{/if}">
            										{$class_item_children}
            										{if !empty($sub_sub_menu.name)}
            											{$sub_sub_menu.name|escape|truncate:60:" ..."}
            										{/if}
            									</a>
            								</li>
            							{/foreach}
            						</ul>
            					{/if}
            				</li>
        				{/foreach}
        	        </ul>
                {/foreach}
    		</li>
    		
    		<li class="d-lg-block d-none" style="flex: 0 0 {$column_flex}%; max-width: {$column_flex}%;" class="pl-0">
                <div class="col_image">
                    {$this->LazyLoad->renderImage([
                        'src' => "{if !empty($data_extend['locale'][{LANGUAGE}]['image_url'])}{CDN_URL}{$this->Block->getLocale('image_url', $data_extend)}{/if}", 
                        'alt' => "{if !empty($data_extend['locale'][{LANGUAGE}]['image_name'])}{$this->Block->getLocale('image_name', $data_extend)}{/if}", 
                        'class' => 'img-fluid h-100'
                    ])}
                    <div>
                        {if !empty($data_extend['locale'][{LANGUAGE}]['image_name'])}
        	        		<h4 class="col_image_title">
        	        			{$this->Block->getLocale('image_name', $data_extend)}
        	        		</h4>
                		{/if}
                		{if !empty($data_extend['locale'][{LANGUAGE}]['btn_link'])}
                			<a class="link_small link_black" href="{$this->Block->getLocale('btn_link', $data_extend)}">
                				{__d('template', 'mua_ngay')}
                			</a>
                		{/if}
                    </div>
                </div>
    		</li>
		</ul>
    </li>
</ul>
{/strip}