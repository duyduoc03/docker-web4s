{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{assign var = element value = "item_album_anh"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}
{strip}

<div class="text-center">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <h3 class=" font-weight-bold text-center color-highlight mb-4">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </h3>
    {/if}
    
    {if !empty($data_extend['locale'][{LANGUAGE}]['mo_ta'])}
        <p class="cus-content-section-small mb-5 w-75 mx-auto">
            {$this->Block->getLocale('mo_ta', $data_extend)}
        </p>
    {/if}
</div>

{if !empty($data_block.data)}
    <div class="swiper" nh-swiper="{if !empty($data_extend.slider_album_anh)}{htmlentities($data_extend.slider_album_anh|@json_encode)}{/if}">
        <div class="swiper-wrapper">
            {foreach from = $data_block.data item = article}
                {$this->element("../block/{$block_type}/{$element}", [
                    'article' => $article, 
                    'is_slider' => $is_slider,
                    'ignore_lazy' => $ignore_lazy
                ])}
            {/foreach}
        </div>
        {if !empty($data_extend.slider_album_anh.pagination)}
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        {/if}
        {if !empty($data_extend.slider_album_anh.navigation)}
            <div class="swiper-button-next">
                <i class="fa-light fa-angle-right h1"></i>
            </div>
            <div class="swiper-button-prev">
                <i class="fa-light fa-angle-left h1"></i>
            </div>
        {/if}
    </div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}
{/strip}