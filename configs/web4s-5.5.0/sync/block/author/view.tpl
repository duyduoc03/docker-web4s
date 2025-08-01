{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = element value = "item"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section text-left">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}
{if !empty($data_block.data)}

    <div class="section-author">
        <div class="row">
            {foreach from = $data_block.data item = author}
                
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="author-item">
                        <div class="inner-image mb-3">
                            <div class="img ratio-1-1">
                                {if !empty($author.avatar)}
                                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($author.avatar, 350)}"}
                                {else}
                                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                                {/if}
                            
                                <a href="{if !empty($author.unique_id)}/author/{$author.unique_id}{/if}" title="{if !empty($author.full_name)}{$author.full_name}{/if}">
                                    {$this->LazyLoad->renderImage([
                                        'src' => $url_img, 
                                        'alt' => "{if !empty($author.full_name)}{$author.full_name}{/if}", 
                                        'class' => 'img-fluid'
                                    ])}
                                </a>
                            </div>
                        </div>

                        <div class="inner-content">
                            {if !empty($author.full_name)}   
                                <div class="author-title mb-2">
                                    <a href="{if !empty($author.url)}{$author.url}{/if}" title="{if !empty($author.full_name)}{$author.full_name}{/if}">
                                        
                                        {$author.full_name}
                                    </a>
                                </div>  
                            {/if}
        
                            {if !empty($author.description)}
                                <div class="article-description mb-0">
                                    {$author.description|strip_tags|truncate:75:" ..."}
                                </div>
                            {/if}
                        </div>  
                    </div>
                </div>
    
            {/foreach}
        </div>
    </div>
{/if}
{if !empty($block_config.has_pagination) && !empty($data_block[{PAGINATION}])}
    {$this->element('pagination_ajax', ['pagination' => $data_block[{PAGINATION}]])}
{/if}
{/strip}