{strip}<nav class="breadcrumbs-section my-3">
	<a href="/">
	    {__d('template', 'trang_chu')}
	</a>
	{if !empty($breadcrumb)}
	    {foreach from = $breadcrumb item = item name = breadcrumb_each}
	        {if !$smarty.foreach.breadcrumb_each.last}
	            {if !empty($item.url)}
    	            <a href="{$this->Utilities->checkInternalUrl($item.url)}">
    	                {if !empty($item.name)}
    	                    {$item.name|escape}
    	                {/if}
    	            </a>
	            {/if}
	        {else}
	            <a href="{$this->Utilities->checkInternalUrl($item.url)}">
	                <span>
                	    {if !empty($item.name)}
    	                    {$item.name|escape}
    	                {/if}
            	    </span>
        	    </a>
	        {/if}
	    {/foreach}
	{/if}
</nav>{/strip}