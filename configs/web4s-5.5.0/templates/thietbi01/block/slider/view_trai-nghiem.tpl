{strip}
{if !empty($data_block)}
    {if !empty($data_extend['locale'][{LANGUAGE}]['image_right'])}
        <div class="transformOnMouse">
            {$this->LazyLoad->renderImage([
               'src' => $this->Utilities->replaceVariableSystem($this->Block->getLocale('image_right', $data_extend)), 
               'class' => 'img-fluid',
               'ignore' => true
            ])}
        </div>
    {/if}
    <div class="row" nh-swiper-thumb>
        <div class="col-12 col-lg-6">
            <div class="swiper" nh-swiper-large="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
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
        	    	
        		        <div class="swiper-slide px-5 pt-5 {if !empty($slider.class_item)}{$slider.class_item}{/if}">
        	                {$this->LazyLoad->renderImage([
                               'src' => $image_url, 
                               'alt' => $slider.name, 
                               'class' => 'img-fluid',
                               'ignore' => true
                            ])}
        		        </div>
        	        {/foreach}
        	    </div>
        	</div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="sl_content">
        		{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de_nho'])}
                    <div class="title-section-short mb-4">
                        {$this->Block->getLocale('tieu_de_nho', $data_extend)}
                    </div>
                {/if}
                {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
                    <h3 class="title-section mb-3">
            			{$this->Block->getLocale('tieu_de', $data_extend)}
            		</h3>
                {/if}
                {if !empty($data_extend['locale'][{LANGUAGE}]['mo_ta'])}
                    <div class="description-section mb-5 pb-4">
                        {$this->LazyLoad->renderContent($this->Block->getLocale('mo_ta', $data_extend))}
                    </div>
                {/if}
                
        		<div class="swiper thumb_trainghiem mb-5 pb-2" nh-slider-thumbs nh-swiper-thumbs="{if !empty($data_extend.slider_thumb)}{htmlentities($data_extend.slider_thumb|@json_encode)}{/if}">
        		    <div class="swiper-wrapper">
        		    	{foreach from = $data_block item = item}
        		    		<div class="swiper-slide">
    			            	<span class="pagination_name">{$item.name}</span>
        			        </div>
        		    	{/foreach}
        		    </div>
        		</div>
        		
        		{if !empty($data_extend['locale'][{LANGUAGE}]['link_lienhe'])}
        			<a class="swiper-slide--link" href="{$this->Block->getLocale('link_lienhe', $data_extend)}">
        				{__d('template', 'lien_he')}
        			</a>
        		{/if}
            </div>
        </div>
    </div>
{/if}

{/strip}