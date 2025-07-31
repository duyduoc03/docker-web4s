{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section color-highlight mb-5">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.data)}
    <div class="swiper" nh-swiper="{if !empty($data_extend.slider_danh_muc)}{htmlentities($data_extend.slider_danh_muc|@json_encode)}{/if}">
        <div class="swiper-wrapper">
            {foreach from = $data_block.data item = cate}
                <div class="swiper-slide mt-0 mb-md-5 mb-4">
                    <div class="item-cate mb-15 mb-sm-0 section-showroom-furniture">
                        <div class="ratio-custome overflow-hidden NH-navGrid zoom-anh-rieng">
                            <a class="text-white" href="{if !empty($cate.url)}{$this->Utilities->checkInternalUrl($cate.url)}{/if}" title="{if !empty($cate.name)}{$cate.name}{/if}">

                                {if !empty($cate.image_avatar)}
                                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($cate.image_avatar, 720)}"}
                                {else}
                                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                                {/if}
                            
                            
                                {$this->LazyLoad->renderImage([
                                    'src' => $url_img, 
                                    'alt' => "{if !empty($cate.name)}{$cate.name}{/if}", 
                                    'class' => 'img-fluid rounded-8'
                                ])}
                            </a>
                        </div>
                        
                       <div class="title-cate-home font-weight-bold py-4">
                            <a class="text-white" href="{if !empty($cate.url)}{$this->Utilities->checkInternalUrl($cate.url)}{/if}" title="{if !empty($cate.name)}{$cate.name}{/if}">
                                {$cate.name}
                            </a>
                        </div>
                        
                        {* <div class="anh-icon-cate-home1">          
                            {if !empty($cate.attributes.iconcate.value)}
                                <img class="anh-icon-cate-home2" src="{CDN_URL}{$cate.attributes.iconcate.value}">
                            {/if}
                        </div> *} 
                        
                    </div>
                </div>
            {/foreach}
        </div>
        {if !empty($data_extend.slider_danh_muc.pagination)}
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        {/if}
        {if !empty($data_extend.slider_danh_muc.navigation)}
            <div class="swiper-button-next">
                <i class="fa-light fa-angle-right h1"></i>
            </div>
            <div class="swiper-button-prev">
                <i class="fa-light fa-angle-left h1"></i>
            </div>
        {/if}
    </div> 
{/if}
{/strip}