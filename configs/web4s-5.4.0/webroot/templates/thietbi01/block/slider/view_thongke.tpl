{strip}
{if !empty($data_block)}
    <div id="counter" class="row mt-5 mt-md-0">
    	{foreach from = $data_block item = slider}
	        <div class="col-6 col-md-3 mb-5 mb-md-0">
	            <div class="item_tk">
	        	    {if !empty($slider.description)}
	        	        <div class="number_tk">
	        	            <span class="count" data-count="{$slider.description}">0</span>
	        	        </div>
                    {/if}
	        		{if !empty($slider.name)}
		        		<h4 class="title_tk mb-0">
		        			{$slider.name}
		        		</h4>
	        		{/if}
	            </div>
	        </div>
        {/foreach}
    </div>
{/if}
{/strip}