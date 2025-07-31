{strip}
{if !empty($data_block)}
	<div class="footer_social">
    	{foreach from = $data_block item = slider}
    	    {if !empty($slider.url) && !empty($slider.description_short)}
        	    <a href="{$slider.url}" class="social_item" target="_blank" rel="nofollow">
    	            {$slider.description_short}
                </a>
            {/if}
        {/foreach}
	</div>
{/if}

{/strip}