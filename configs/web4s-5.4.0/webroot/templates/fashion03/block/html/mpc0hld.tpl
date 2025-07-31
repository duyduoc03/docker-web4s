{strip}<div class="bg-feedback" {if !empty($data_extend['locale'][{LANGUAGE}]['anh_banner'])}nh-lazy="image-background" data-src="{CDN_URL}{$this->Block->getLocale('anh_banner', $data_extend)}"{/if}>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-4">
                {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
                	<h3 class="title-section">
                		{$this->Block->getLocale('tieu_de', $data_extend)}
                		
                		<div class="line-title">
                    		{$this->LazyLoad->renderImage([
                            	'src' => "{CDN_URL}/media/template/bottom-title.webp", 
                            	'alt' => "image",
                            	'class' => 'img-fluid object-contant'
                            ])}
                		</div>
                	</h3>
                {/if}
            </div>
            <div class="col-12 col-md-8">
                {if !empty($data_extend.data_collection[{LANGUAGE}])}
                    <div class="swiper feedback-box" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
                        <div class="swiper-wrapper">
                            {foreach from = $data_extend.data_collection[{LANGUAGE}] key = key item = item}
                                <div class="feedback-item text-center swiper-slide">
                                    {if !empty($item.content)}
                                        <div class="inner-content">
                                            <i class="fas fa-quote-right"></i>
                                            {$item.content}
                                        </div>
                                    {/if}
                                    
                                    <div class="d-flex justify-content-center align-items-center my-4">
                                        <div class="inner-name">
                                            {if !empty($item.name)}
                                                {$item.name}
                                            {/if}
                                        </div>
                                        <span class="mx-2">-</span>
                                        <div class="inner-job">
                                             {if !empty($item.job)}
                                                {$item.job}
                                            {/if}
                                        </div>
                                    </div>
                                    
                                    <div class="inner-image">
                                        {if !empty($item.image)}
                                            {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item.image, 350)}"}
                                        {else}
                                            {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                                        {/if}
                                        
                                        {$this->LazyLoad->renderImage([
                                            'src' => $url_img, 
                                            'alt' => "{if !empty($item.name)}{$item.name}{/if}", 
                                            'class' => 'img-fluid'
                                        ])}
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                        {if !empty($data_extend.slider.pagination)}
                            <!-- If we need pagination -->
                            <div class="swiper-pagination"></div>
                        {/if}
                        {if !empty($data_extend.slider.navigation)}
                            <div class="swiper-button-next">
                                <i class="fa-light fa-angle-right h1"></i>
                            </div>
                            <div class="swiper-button-prev">
                                <i class="fa-light fa-angle-left h1"></i>
                            </div>
                        {/if}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>{/strip}