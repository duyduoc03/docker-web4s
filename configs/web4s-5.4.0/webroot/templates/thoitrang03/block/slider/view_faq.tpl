{strip}
<div class="row align-items-center">
    <div class="col-md-5 col-12">
        {if !empty($data_extend['locale'][{LANGUAGE}]['anh'])}
            <div class="cus-anh-banner-1 ratio-1-1 ">
                {$this->LazyLoad->renderImage([
                    'src' => " {CDN_URL}{$this->Block->getLocale('anh', $data_extend)}", 
                    'alt' => "image", 
                    'class' => 'img-fluid rounded-8'
                ])}
            </div>
        {/if}
    </div>

    <div class="col-md-7 col-12 mt-md-0 mt-5">
        {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
            <h3 class="title-section color-highlight mb-5">
                {$this->Block->getLocale('tieu_de', $data_extend)}
            </h3>
        {/if}

        {$tab_id = "tab-{time()}-{rand(1, 100)}"}
        {if !empty($data_block)}
        	<div class="faq-main">
               <div class="">
                    <div class="acc-programs" id="accordion">
                        {foreach from = $data_block key = key item = slider}
                            {assign var = image_source value = ''}
                    		{if !empty($slider.image) && !empty($slider.image_source)}
                    			{assign var = image_source value = $slider.image_source}
                    		{/if}
                    
                    		{assign var = image_url value = ''}
                    		{if !empty($slider.image) && $image_source == 'cdn'}
                    			{assign var = image_url value = "{CDN_URL}{$slider.image}"}
                    			{if !empty(DEVICE)}
                    			    {assign var = image_url value = "{CDN_URL}{$this->Utilities->getThumbs($slider.image, 50)}"}
                    			{/if}
                    		{/if}
                    
                    		{if !empty($slider.image) && $image_source == 'template'}
                    			{assign var = image_url value = "{$slider.image}"}
                    			{if !empty(DEVICE)}
                    			    {assign var = image_url value = "{$this->Utilities->getThumbs($slider.image, 50, 'template')}"}
                    			{/if}
                    		{/if}
                    		
                    		{if !empty($slider.name)}
                                <div class="card">
                                    <div class="card-header" id="heading{$tab_id}-{$key}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{$tab_id}-{$key}" aria-expanded="true" aria-controls="collapse{$tab_id}-{$key}">
                                                {$slider.name} <i class="fa-solid fa-circle-chevron-down"></i>
                                            </button>
                                        </h5>
                                    </div>
                            
                                    <div id="collapse{$tab_id}-{$key}" class="collapse" aria-labelledby="heading{$tab_id}-{$key}" data-parent="#accordion">
                                        <div class="card-body p-4">
                                            {if !empty($slider.description)}
                                                {$slider.description}
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    </div>
               </div>
        	</div>
        {/if}
    </div>
</div>

{/strip}