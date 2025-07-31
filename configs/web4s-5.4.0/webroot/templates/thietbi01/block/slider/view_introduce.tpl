{strip}
<div class="bg-intro py-10r">
    {if !empty($data_extend['locale'][{LANGUAGE}]['background-left'])}
        {$this->LazyLoad->renderImage([
             'src' => $this->Utilities->replaceVariableSystem($this->Block->getLocale('background-left', $data_extend)), 
             'alt' => 'background left', 
             'class' => 'bg-intro-left d-none'
          ])}
    {/if}
    {if !empty($data_extend['locale'][{LANGUAGE}]['background-right'])}
        {$this->LazyLoad->renderImage([
             'src' => $this->Utilities->replaceVariableSystem($this->Block->getLocale('background-right', $data_extend)), 
             'alt' => 'background right', 
             'class' => 'bg-intro-right d-none'
          ])}
    {/if}

    <div class="container">
        {if !empty($data_block)}
            <div class="row col-px-60">
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
            		{if $slider@index == 0}
            		    <div class="col-12 col-lg-6 mb-5 mb-lg-0 pb-4 pb-lg-0{if !empty($slider.class_item)} {$slider.class_item}{/if}">
                            <div>
                                {$this->LazyLoad->renderImage([
                                     'src' => $image_url, 
                                     'alt' => $slider.name, 
                                     'class' => 'img-fluid',
                                     'ignore' => true
                                  ])}
                            </div>
                        </div>
                    {else if $slider@index == 1}
                        <div class="col-12 col-lg-6{if !empty($slider.class_item)} {$slider.class_item}{/if}">
                            <div class="sl_content">
                        		{if !empty($slider.description_short)}
                                    <div class="title-section-short mb-4">
                                        {$slider.description_short}
                                    </div>
                                {/if}
             
                                <h3 class="title-section mb-3">
            	        			{$slider.name}
            	        		</h3>
                               
                                {if !empty($slider.description)}
                                    <div class="description-section mb-5 pb-lg-5">
                                        {$this->LazyLoad->renderContent($slider.description)}
                                    </div>
                                {/if}
                                {$this->LazyLoad->renderImage([
                                     'src' => $image_url, 
                                     'alt' => $slider.name, 
                                     'class' => 'img-fluid w-100',
                                     'ignore' => true
                                  ])}
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
        {/if}
    </div>
</div>
{/strip}