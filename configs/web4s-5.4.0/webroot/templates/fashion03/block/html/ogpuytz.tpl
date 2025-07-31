{strip}<div class="box-about-home bg-container bg-about">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="img ratio-3-2">
            	{$this->LazyLoad->renderImage([
            		'src' => "{if !empty($data_extend['locale'][{LANGUAGE}]['image'])}{$this->Utilities->replaceVariableSystem($this->Block->getLocale('image', $data_extend))}{/if}", 
            		'alt' => "{if !empty($data_extend['locale'][{LANGUAGE}]['alt'])}{$this->Block->getLocale('alt', $data_extend)}{/if}",
            		'class' => 'img-fluid'
            	])}
            </div>
        </div>
        <div class="col-12 col-md-6">
            {if !empty($data_extend.data_collection[{LANGUAGE}])}
                <div class="about-home">
                    {foreach from = $data_extend.data_collection[{LANGUAGE}] key = key item = item}
                        {if !empty($item.tieu_de)}
                            <div class="title-about">
                                {$item.tieu_de}
                            </div>
                        {/if}
                        
                         {if !empty($item.mo_ta)}
                            <div class="desc-about">
                                {$item.mo_ta}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
</div>{/strip}