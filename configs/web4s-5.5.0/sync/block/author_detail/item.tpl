{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-6 col-md-4 col-lg-3{/if}">
{/if}

<article class="article-item bg-white swiper-slide">
    <div class="inner-image">
        <div class="ratio-3-2">
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
        
        <div class="category-view d-flex justify-content-between align-items-center mb-4">
            {if !empty($article.categories) && empty(DEVICE)}
                <span class="article-category ">
                    {foreach from = $article.categories item = category}
                        {if !empty($category.name)}
                            <a class="d-inline-block" href="{$this->Utilities->checkInternalUrl($category.url)}">
                                {$category.name|escape|truncate:50:" ..."}
                            </a>
                        {/if}
                    {/foreach}
                </span>
            {/if}
            {if !empty($article.view)}
                <span class="view">
                    <i class="iconsax isax-eye4"></i>
                    <span>
                        {$article.view}
                    </span>
                    view
                </span>
            {/if}
        </div>
        {if !empty($article.name)}   
            <div class="article-title mb-3">
                <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name}
                </a>
            </div>  
        {/if}

        
        
        {if !empty($article.updated)}
            <div class="post-date">
                {$this->Utilities->convertIntgerToDateString($article.updated)}
            </div>
        {else}
            <div class="post-date">
                {$this->Utilities->convertIntgerToDateString($article.created)}
            </div>
        {/if}

        {if !empty($article.description)}
            <div class="article-description mb-0">
                {$article.description|strip_tags|truncate:75:" ..."}
            </div>
        {/if}
    </div>  
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}