{strip}
<div class="breadcrum_article">
    <div class="breadcrum_content" {if !empty($data_extend['locale'][{LANGUAGE}]['background'])}style="background-image: url({URL_TEMPLATE}{$this->Block->getLocale('background', $data_extend)});"{/if}>
        <div class="container">
            {if !empty($breadcrumb)}
        	    {foreach from = $breadcrumb item = item name = breadcrumb_each}
                    {if $item@last}
        	            {if !empty($item.name)}
            	            <h1>
        	                    {$item.name}
                        	</h1>
                    	{/if}
        	        {/if}
        	    {/foreach}
        	{/if}
        
            {if !empty($data_block.data)}
                <div nh-menu="active">
                    {$this->element('../block/category_article/item_breadcrum', [
                    	'categories' => $data_block.data
                    ])}
                </div>
            {/if}
        </div>
    </div>
</div>
{/strip}