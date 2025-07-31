{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-6 col-md-4 col-lg-4{/if}">
{/if}

<article class="article-item wrp-effect-scale swiper-slide">
    <div class="inner-image mb-3">
        <div class="ratio-1-1">
            {if !empty($article.image_avatar)}
                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 350)}"}
            {else}
                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
            {/if}
        
            <a href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}" title="{if !empty($article.name)}{$article.name}{/if}">
                {$this->LazyLoad->renderImage([
                    'src' => $url_img, 
                    'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                    'class' => 'img-fluid',
                    'ignore' => $ignore
                ])}
            </a>
        </div>
    </div>
    <div class="inner-content">
        {if !empty($article.name)}   
            <h4 class="article-title mb-2">
                <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name|escape|truncate:50:" ..."}
                </a>
            </h4>  
        {/if}

        {if !empty($article.description)}
            <div class="article-description mb-3">
                {$article.description|strip_tags|truncate:85:" ..."}
            </div>
        {/if}
    </div>  
    
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}