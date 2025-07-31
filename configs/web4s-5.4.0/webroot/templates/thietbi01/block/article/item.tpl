{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-12 col-md-6 col-lg-4 mb-5{/if}">
{/if}

<article class="article-item swiper-slide page_list_article">
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
    <div class="inner-content">
        <div class="post-date mt-4 mb-3">
            {if !empty($article.created)}
                {$this->Utilities->convertIntgerToDateString($article.created, 'd ')}
                {__d('template', 'thang')} {$this->Utilities->convertIntgerToDateString($article.created, 'm, Y')}
            {/if}
        </div>
        
        {if !empty($article.name)}   
            <div class="article-title my-2">
                <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{$article.name}">
                    {$article.name}
                </a>
            </div>  
        {/if}

        {if !empty($article.description)}
            <div class="article-description mb-3">
                {$article.description|strip_tags}
            </div>
        {/if}

        <a class="link_small link_black show_70" href="{$this->Utilities->checkInternalUrl($article.url)}">
            {__d('template', 'xem_them')}
        </a>
    </div>  
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}