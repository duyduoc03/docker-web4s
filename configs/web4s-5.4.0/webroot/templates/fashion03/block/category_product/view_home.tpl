{strip}
<div class="decoration-block bg-light p-5">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <h3 class="title-section mb-4">
        	{$this->Block->getLocale('tieu_de', $data_extend)}
        </h3>
    {/if}
    
    {if !empty($data_block.data)}
        <div nh-menu="active">
            {$this->element('../block/category_product/item', [
            	'categories' => $data_block.data,
            	'parent_id' => null
            ])}
        </div>
    {/if}
</div>

{if !empty($data_extend['locale'][{LANGUAGE}]['banner'])}
    <div class="ratio-custome mt-4">
        {$this->LazyLoad->renderImage([
            'src' => "{CDN_URL}{$this->Block->getLocale('banner', $data_extend)}", 
            'alt' => "banner", 
            'class' => 'img-fluid'
        ])}
    </div>
{/if}
{/strip}