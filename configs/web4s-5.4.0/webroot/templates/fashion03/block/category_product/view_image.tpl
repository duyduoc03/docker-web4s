{strip}
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <h3 class="title-section text-center mb-5">
        	{$this->Block->getLocale('tieu_de', $data_extend)}
        </h3>
    {/if}
    
    <div class="row justify-content-center">
        {if !empty($data_block.data)}
            {$this->element('../block/category_product/item_img', [
            	'categories' => $data_block.data,
            	'parent_id' => null
            ])}
        {/if}
    </div>
{/strip}