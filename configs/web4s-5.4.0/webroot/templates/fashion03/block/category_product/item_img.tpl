{strip}
{if !empty($categories)}
	{foreach from = $categories item = category}
		<div class="col-6 col-md-4">
			<a class="product-item--cate" {if !empty($category.url)}href="{$this->Utilities->checkInternalUrl($category.url)}"{/if}>
			    {if !empty($category.image_avatar)}
                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($category.image_avatar, 350)}"}
                {else}
                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}
			    
			    <div class="ratio-16-9">
		            {$this->LazyLoad->renderImage([
                        'src' => $url_img, 
                        'alt' => "{if !empty($category.name)}{$category.name}{/if}", 
                        'class' => 'img-fluid object-contant'
                    ])}
			    </div>
			    
			    {if !empty($category.name)}
    			    <div class="prd-cate-title text-center">
    				    {$category.name|escape|truncate:80:" ..."}
    			    </div>
			    {/if}
			</a>
		</div>
	{/foreach}
{/if}
{/strip}