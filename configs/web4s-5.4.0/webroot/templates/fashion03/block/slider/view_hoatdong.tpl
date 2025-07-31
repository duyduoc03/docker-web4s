{strip}
{if !empty($data_block)}
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <h3 class="title-section text-center mb-5">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </h3>
    {/if}
	<div class="swiper swiper-how-work" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
	    <div class="swiper-wrapper">
	    	{foreach from = $data_block key=key item = slider}
	    		{assign var = image_source value = ''}
				{if !empty($slider.image) && !empty($slider.image_source)}
					{assign var = image_source value = $slider.image_source}
				{/if}

				{assign var = image_url value = ''}
				{if !empty($slider.image) && $image_source == 'cdn'}
					{assign var = image_url value = "{CDN_URL}{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{CDN_URL}{$this->Utilities->getThumbs($slider.image, 350)}"}
					{/if}
				{/if}

				{if !empty($slider.image) && $image_source == 'template'}
					{assign var = image_url value = "{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{$this->Utilities->getThumbs($slider.image, 350, 'template')}"}
					{/if}
				{/if}
				
		        <div class="swiper-slide item-how-work text-center {if !empty($slider.class_item)}{$slider.class_item}{/if}">
		            <img src="{$image_url}" class="img-fluid" alt="{if !empty($slider.name)}{$slider.name}{/if}">
		        	
		        	<div class="how-work--wrap mt-4" data-swiper-parallax="-300">
		        	    {if !empty($slider.name)}
		        	    	<div class="how-work--tile">
			        			{$slider.name}
			        		</div>
		        		{/if}
		        		
		        		{if !empty($slider.description)}
		        			<div class="how-work--description my-3">
			        			{$slider.description}
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