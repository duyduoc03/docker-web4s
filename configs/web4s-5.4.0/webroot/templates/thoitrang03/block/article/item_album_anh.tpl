{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}


<article class="cus-album-item swiper-slide mt-0 mb-3">

    {if !empty($article.image_avatar)}
        {assign var = thumgb_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 720)}"}
        {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 720)}"}
        {assign var = full_img value = "{CDN_URL}{$article.image_avatar}"}
    {else}
        {assign var = thumb_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
        {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
        {assign var = full_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
    {/if}

    <div nh-light-gallery>
        <a class="wrp-effect-album" href="{$full_img}" title="{if !empty($article.name)}{$article.name}{/if}">
            <div class="inner-image ratio-1-1 effect-gallery effect-video">
                {$this->LazyLoad->renderImage([
                    'src' => $url_img, 
                    'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                    'class' => 'img-fluid rounded-8'
                ])}
            </div>
        </a>
        {if !empty($article.images)}
            {foreach from = $article.images item = image}
                <div class="d-none" data-src="{CDN_URL}{$image}">
                    {$this->LazyLoad->renderImage([
                        'src' => "{CDN_URL}{$image}", 
                        'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                        'class' => 'img-fluid rounded-8',
                        'delay' => 'all'
                    ])}
                </div>
            {/foreach}
        {/if}
    </div>
</article>
{/strip}