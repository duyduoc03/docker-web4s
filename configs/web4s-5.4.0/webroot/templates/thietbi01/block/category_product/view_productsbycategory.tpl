{strip}
<div class="title_has_link {if !empty($data_extend['locale'][{LANGUAGE}]['btn_view'])} mb-lg-5 mb-4 {/if}">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <h3 class="title-section">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </h3>
    {/if}
    
    {if !empty($data_extend['locale'][{LANGUAGE}]['btn_view'])}
        <a class="btn_all link_small link_black" href="{if !empty($data_extend['locale'][{LANGUAGE}]['url_view'])}{$this->Block->getLocale('url_view', $data_extend)}{/if}">
            {$this->Block->getLocale('btn_view', $data_extend)}
        </a>
    {/if}
</div>
<div class="categories swiper" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
    <div class="swiper-wrapper">
        {if !empty($data_block.data)}
    		{foreach from = $data_block.data item = category}
    			<div class="swiper-slide text-center">
    			    <div>
    			        <a href="{$this->Utilities->checkInternalUrl($category.url)}" title="{if !empty($category.name)}{$category.name}{/if}">
                            {$this->LazyLoad->renderImage([
                                'src' => "{if !empty($category.image_avatar)}{CDN_URL}{$category.image_avatar}{/if}", 
                                'alt' => "{if !empty($category.name)}{$category.name}{/if}", 
                                'class' => 'img-fluid category_radius'
                            ])}
                        </a>
    			    </div>
    			    <div class="pt-4 pb-3">
    			        {if !empty($category.name)}
    			            <a class="title_tk link_title hv_40" {if !empty($category.url)}href="{$this->Utilities->checkInternalUrl($category.url)}"{/if}>
            					{$category.name|escape|truncate:80:" ..."}
            				</a>
        				{/if}
    			    </div>
    			</div>
    		{/foreach}
        {/if}
    </div>
</div>
{/strip}