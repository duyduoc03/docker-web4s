{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-12 col-sm-6 col-md-4 mb-5{/if}">
{/if}

<article class="article-item home_article swiper-slide bg-white bd_rds-8">
    <div class="inner-image">
        <div class="ratio-4-3">
            {if !empty($article.image_avatar)}
                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 500)}"}
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
    <div class="inner-content p-2r">
        <div class="post-date mb-4">
            {if !empty($article.created)}
                {$this->Utilities->convertIntgerToDateString($article.created, 'd ')}
                {__d('template', 'thang')} {$this->Utilities->convertIntgerToDateString($article.created, 'm, Y')}
            {/if}
        </div>
        {if !empty($article.name)}   
            <h4 class="article-title height_max mb-3">
                <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name}
                </a>
            </h4>  
        {/if}

        {if !empty($article.description)}
            <div class="article-description mb-3">
                {$article.description|strip_tags}
            </div>
        {/if}

        <a class="link_title" href="{$this->Utilities->checkInternalUrl($article.url)}">
            {__d('template', 'xem_them')}
            <i class="fa-solid fa-angle-right pl-2"></i>
        </a>
    </div>  
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}