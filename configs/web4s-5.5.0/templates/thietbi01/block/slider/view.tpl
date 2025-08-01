{strip}
{if !empty($data_block)}
	<div class="swiper swiper-slider-main" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
	    <div class="swiper-wrapper">
	    	{foreach from = $data_block item = slider}
	    		{assign var = image_source value = ''}
				{if !empty($slider.image) && !empty($slider.image_source)}
					{assign var = image_source value = $slider.image_source}
				{/if}

				{assign var = image_url value = ''}
				{if !empty($slider.image) && $image_source == 'cdn'}
					{assign var = image_url value = "{CDN_URL}{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{CDN_URL}{$this->Utilities->getThumbs($slider.image, 720)}"}
					{/if}
				{/if}

				{if !empty($slider.image) && $image_source == 'template'}
					{assign var = image_url value = "{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{$this->Utilities->getThumbs($slider.image, 720, 'template')}"}
					{/if}
				{/if}
				
		        <div class="swiper-slide {if !empty($slider.class_item)}{$slider.class_item}{/if}">
		            {if $slider@first}
		                <img src="{$image_url}" class="img-fluid w-100" alt="{$slider.name}">
		            {else}
		                <img data-src="{$image_url}" class="swiper-lazy img-fluid w-100" alt="{$slider.name}">
		                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
		            {/if}
		        	
		        	<div class="swiper-slide--wrap container" data-swiper-parallax="-300">
		        	    {if !empty($slider.description_short)}
		        			<div class="swiper-slide--description_short">
			        			{$slider.description_short}
			        		</div>
		        		{/if}
		        		
		        		{if $slider@first}
    		                {if !empty($slider.name)}
    			        		<h1 class="swiper-slide--tile">
    			        			{$slider.name}
    			        		</h1>
    		        		{/if}
    		            {else}
    		                {if !empty($slider.name)}
    			        		<div class="swiper-slide--tile">
    			        			{$slider.name}
    			        		</div>
    		        		{/if}
    		            {/if}
    		            
		        		{if !empty($slider.url)}
		        			<a class="swiper-slide--link" href="{$slider.url}" nh-to-anchor="{$slider.url}" title="{$slider.name}">
		        				{__d('template', 'thuc_don')}
		        			</a>
		        		{/if}
		        		
		        		{if !empty($data_extend['locale'][{LANGUAGE}]['sales'])}
		        		    <div class="sale_content d-none d-lg-flex mt-6r">
		        		        {foreach from = $this->Block->getLocale('sales', $data_extend) item = sale}
    		        		        {if !empty($sale.name)}
        		        		        <div class="{if !$sale@last}mr-4{/if}">
        		        		            <div class="sale_item d-inline-flex align-items-center h-100 bg-white py-5 px-2r">
            		        		            {if !empty($sale.image)}
                		        		            <div class="mr-3">
                		        		                {$this->LazyLoad->renderImage([
                                                            'src' => $this->Utilities->replaceVariableSystem($sale.image), 
                                                            'alt' => $sale.name, 
                                                            'class' => 'img-fluid',
                                                            'ignore' => true
                                                        ])}
                		        		            </div>
            		        		            {/if}
            		        		            <div>
                		        		            <h4 class="sale_title">
                		        		                {if !empty($sale.off)}
                        		        		            <span>
                        		        		                -{$sale.off}%
                        		        		            </span>
                    		        		            {/if}
                    		        		            {$sale.name}
                		        		            </h4>
                		        		            {if !empty($sale.description)}
                    		        		            <div>
                    		        		                {$sale.description}
                    		        		            </div>
                		        		            {/if}
            		        		            </div>
            		        		        </div>
        		        		        </div>
    		        		        {/if}
    		        		    {/foreach}
		        		    </div>
		        		{/if}
		        	</div>
		        </div>
	        {/foreach}
	    </div>
	    {if !empty($data_extend.slider.pagination)}
		    <!-- If we need pagination -->
		    <div class="swiper-pagination"></div>
	    {/if}

	    {if !empty($data_extend.slider.navigation)}
		    <!-- If we need navigation buttons -->
		    <div class="swiper-button-next">
	            <i class="fa-light fa-angle-right display-2"></i>
	        </div>
	        <div class="swiper-button-prev">
	            <i class="fa-light fa-angle-left display-2"></i>
            </div>
        {/if}
	</div>
{/if}

{/strip}