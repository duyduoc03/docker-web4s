{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de_nho'])}
        <div class="title-section-short mb-4">
            {$this->Block->getLocale('tieu_de_nho', $data_extend)}
        </div>
    {/if}
    <div class="row mb-lg-5 mb-4">
        <div class="col-12 col-lg-4">
            <h3 class="title-section">
                {$this->Block->getLocale('tieu_de', $data_extend)}
            </h3>
        </div>
        
        <div class="col-12 col-lg-6">
            {if !empty($data_extend['locale'][{LANGUAGE}]['mo_ta'])}
                <div class="description-section w-xxl-78">
                    {$this->Block->getLocale('mo_ta', $data_extend)}
                </div>
            {/if}
        </div>
        
        <div class="col-12 col-lg-2">
            {if !empty($data_extend['locale'][{LANGUAGE}]['url_view'])}
                <a class="btn_readmore text-uppercase" href="{$this->Block->getLocale('url_view', $data_extend)}">
        			{__d('template', 'xem_them')}
        			<i class="fa-solid fa-arrow-right"></i>
        		</a>
            {/if}
        </div>
    </div>
{/if}

{if !empty($data_block.data)}
    <div class="ar_gallery_container">
        {foreach from = $data_block.data item = article}
            {if !empty($article.url)}
                <article class="article-item ar_gallery_item">
                    <div class="inner-image position-static">
                        {if !empty($article.image_avatar)}
                            {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article.image_avatar, 350)}"}
                        {else}
                            {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                        {/if}
                    
                        <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{$article.name}">
                            {$this->LazyLoad->renderImage([
                                'src' => $url_img, 
                                'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                                'class' => 'img-fluid h-100 w-100',
                                'ignore' => true
                            ])}
                        </a>
                    </div>
                    <div class="inner-content">
                        <div>
                            {if !empty($article.name)}   
                                <div class="article-title my-2">
                                    <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                                        {$article.name}
                                    </a>
                                </div>  
                            {/if}
                            {if !empty($article.categories)}
                                <span class="article-category d-block mb-4">
                                    {foreach from = $article.categories item = category}
                                        {if !empty($category.url) && $category@last}
                                            <a class="bg-cate d-inline-block" href="{$this->Utilities->checkInternalUrl($category.url)}">
                                                {$category.name|escape}
                                            </a>
                                        {/if}
                                    {/foreach}
                                </span>
                            {/if}
                        </div>
                    </div>  
                </article>
            {/if}
        {/foreach}
    </div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}

{if !empty($block_config.has_pagination) && !empty($data_block[{PAGINATION}])}
    {$this->element('pagination_ajax', ['pagination' => $data_block[{PAGINATION}]])}
{/if}
{/strip}