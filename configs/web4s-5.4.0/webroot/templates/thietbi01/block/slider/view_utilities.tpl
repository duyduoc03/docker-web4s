{strip}
{if !empty($data_block)}
    <div class="row">
    	{foreach from = $data_block item = slider}
	        <div class="col-12 col-md-6 col-xl-3 text-white">
                <div class="item_tk d-flex">
                    <div>
                        {if !empty($slider.description_short)}
    		        	    <span class="icon_tk">
    		        	        {$this->LazyLoad->renderContent($slider.description_short)}
                            </span>
                        {/if}
                    </div>
                    
                    <div class="pl-4">
                        {if !empty($slider.name)}
    		        		<h5 class="title_tk">
    		        			{$slider.name}
    		        		</h5>
    	        		{/if}
    	        		{if !empty($slider.description)}
                			<div class="description_tk">
        	        			{$slider.description}
        	        		</div>
                		{/if}
                    </div>
        		</div>
	        </div>
        {/foreach}
    </div>
{/if}

{/strip}