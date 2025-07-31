{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-12 col-md-4 col-lg-4 mb-lg-5 mb-4{/if}">
{/if}

<article class="article-item wrp-effect-scale swiper-slide mb-0">
    <div class="inner-image">
        <div class="ratio-16-9">
            {if !empty($article.image_avatar)}
                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 350)}"}
            {else}
                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
            {/if}
        
            <a href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}" title="{if !empty($article.name)}{$article.name}{/if}">
                {$this->LazyLoad->renderImage([
                    'src' => $url_img, 
                    'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                    'class' => 'img-fluid rounded',
                    'ignore' => $ignore
                ])}
            </a>
        </div>
    </div>
    <div class="inner-content">
        <div class="d-flex justify-content-between align-items-center my-4">
            {if !empty($article.categories)}
                <span class="article-category ">
                    {foreach from = $article.categories item = category}
                        {if !empty($category.name)}
                            <a class="bg-cate bg-id{$category.id} d-inline-block {if !$category@last}mr-2{/if}" href="{$this->Utilities->checkInternalUrl($category.url)}">
                                {$category.name|escape|truncate:50:" ..."}
                            </a>
                        {/if}
                    {/foreach}
                </span>
            {/if}
            
            {assign var = data_view value = $this->Article->getDetailArticle({$article.id}, {LANGUAGE}, [
                'get_user' => true
            ])}
            <span class="view text-secondary">
                <i class="fa-solid fa-eye pr-2"></i>
                {if !empty($data_view.view)}
                    {$data_view.view + 125} View
                {else}
                    125 View
                {/if}
            </span>
        </div>
       
        {if !empty($article.name)}   
            <h4 class="article-title mb-2">
                <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name|escape|truncate:70:" ..."}
                </a>
            </h4>  
        {/if}

        {if !empty($article.description)}
            <div class="article-description">
                {$article.description|strip_tags|truncate:200:" ..."}
            </div>
        {/if}

        <a class="color-black" href="{$this->Utilities->checkInternalUrl($article.url)}">
            {__d('template', 'xem_them')} <i class="fa-light fa-arrow-right"></i>
        </a>
    </div>
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}