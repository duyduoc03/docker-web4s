{strip}
{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = item value = "item_slider"}
{if !empty($data_extend['item'])}
    {assign var = item value = $data_extend['item']}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}
<div class="container position-relative">
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
    
    {if !empty($data_block.data)}
        <div class="swiper" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
            <div class="swiper-wrapper">
                {foreach from = $data_block.data item = product}
                    {$this->element("../block/{$block_type}/{$item}", [
                        'product' => $product, 
                        'is_slider' => true,
                        'ignore_lazy' => $ignore_lazy
                    ])}
                {/foreach}
            </div>
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
    {else}
        <div class="mb-4">
            {__d('template', 'khong_co_du_lieu')}
        </div>
    {/if}
</div>
{/strip}