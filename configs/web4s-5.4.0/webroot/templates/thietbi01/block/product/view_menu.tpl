{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <div class="text-center" nh-anchor="our_menu">
        {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de_nho'])}
            <div class="title-section-short mb-4">
                {$this->Block->getLocale('tieu_de_nho', $data_extend)}
            </div>
        {/if}
        <h2 class="title-section title-menu">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </h2>
    </div>
{/if}

{if !empty($data_block.data)}
    <div class="menu-restaurant">
        <div class="row col-px-30">
            {foreach from = $data_block.data item = item}
                <div class="col-12 col-md-6">
                    <div class="item-menu">
                        <div class="inner-content">
                            <h4 class="title-name pb-2">
                                {if !empty($item.name)}
                                    {$item.name}
                                {/if}
                            </h4>
                            <p class="desc">
                                {if !empty($item.description)}
                                    {$item.description|strip_tags|truncate:55:"...":true}
                                {/if}
                            </p>
                            <p class="price text-right">
                                {if !empty($item.price)}
                                    {$product.price|number_format:0:".":","}
                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>      
            {/foreach}
        </div>
    </div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}
{/strip}