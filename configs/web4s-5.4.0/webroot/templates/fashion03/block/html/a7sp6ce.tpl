{strip}<div class="box-sp-noi-bat" nh-lazy="image-background" data-src="{CDN_URL}/media/slider/service-2.webp"  >
    <div class="container">
        <div class="row align-items-center">
            {foreach from = $data_extend.data_collection[{LANGUAGE}] key = key item = item}
                <div class="col-12 col-md-6 order-md-1 d-lg-none">
                    {if !empty($item.anh)}
                        <div class="ratio-4-3">
                            {$this->LazyLoad->renderImage([
                                'src' => "{CDN_URL}{$item.anh}", 
                                'alt' => "image", 
                                'class' => 'img-fluid'
                            ])}
                        </div>
                    {/if}
                </div>
                <div class="col-12 col-md-6 ">
                    <div class="featured-home">
                        {if !empty($item.tieu_de)}
                            <div class="title-featured-home">
                                {$item.tieu_de}
                            </div>
                        {/if}
                        
                         {if !empty($item.mo_ta)}
                            <div class="desc-featured-home">
                                {$item.mo_ta}
                            </div>
                        {/if}
                    </div>
                    
                    {if !empty($item.url)}
                        <a class="btn btn-submit-1" href="{$item.url}">
                            {__d('template', 'xem_chi_tiet')} 
                        </a>
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>
</div>{/strip}