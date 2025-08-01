<div class="wrap-suggestion suggestion_home d-block d-lg-none p-2 pb-3">
	{if !empty($products)}
		<div class="font-weight-bold h5 m-3">
			{__d('template', 'san_pham')}
		</div>
	    <ul class="list-unstyled mb-0">
	        {foreach from = $products item = product}
	            {if !empty($product['image'])}
	                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['image'], 150)}"}
	            {else}
	                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
	            {/if}
	            
	            <li class="px-3 py-2">
	                <a class="row mx-n2 color-main" href="/{$product.url}">
	                	<div class="col-3 col-lg-1 px-2">
	                		<div class="ratio-1-1">
								<img src="{$url_img}" alt="{$product.name}" class="img-fluid">
							</div>
						</div>
	                	<div class="col-9 col-lg-11 px-2">
	                		{if !empty($product.name)}
	                    		<div>
	                    			{$product.name|truncate:45:" ..."}
	                    		</div>
	                		{/if}
	            			<div class="price suggest-price">
		                        {if !empty($product.price)}                        
                                    <span class="price-amount">
                                        {if empty($product.apply_special) && !empty($product.price)}
                                            {$product.price|number_format:0:".":","}
                                            <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                        {/if}
                    
                                        {if !empty($product.apply_special) && !empty($product.price_special)}
                                            {$product.price_special|number_format:0:".":","}
                                            <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                        {/if}
                                    </span>  
                                
                                
                                    {if !empty($product.apply_special) && !empty($product.price)}
                                        <span class="price-amount old-price">
                                            {$product.price|number_format:0:".":","}
                                            <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                        </span>
                                    {/if}
                                {else}
                                    <span class="price-amount">
                                        {__d('template', 'lien_he')}
                                    </span>  
                                {/if}
	            			</div>
	                	</div>
	                </a>
	            </li>
	        {/foreach}
	    </ul>
	{/if}

	{if !empty($articles)}
		<div class="font-weight-bold h5 m-3">
			{__d('template', 'tin_tuc')}
		</div>
	    <ul class="list-unstyled mb-0">
	        {foreach from = $articles item = article}
	            {if !empty($article['image'])}
	                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article['image'], 150)}"}
	            {else}
	                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
	            {/if}
	            <li class="px-3 py-2">
	                <a class="row mx-n2 color-main" href="/{$article.url}">
	                	<div class="col-4 col-lg-1 px-2">
	                		<div class="ratio-4-3">
								<img src="{$url_img}" alt="{$article.name}" class="img-fluid">
							</div>
						</div>
	                	<div class="col-8 col-lg-11 px-2">
	                		{if !empty($article.name)}
	                    		<div>
	                    			{$article.name|truncate:45:" ..."}
	                    		</div>
	                		{/if}
	                	</div>
	                </a>
	            </li>
	        {/foreach}
	    </ul>
	{/if}
</div>
<div class="wrap-suggestion suggestion_page d-none d-lg-block">
	{if !empty($products)}
	    <div class="row">
	        {foreach from = $products item = product}
	            {if $product@index < 5}
    	            {if !empty($product['image'])}
    	                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['image'], 350)}"}
    	            {else}
    	                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
    	            {/if}
    	            
    	            <li class="d-block col-lg-3 col-lg-custom">
    	                <a class="color-main" href="/{$product.url}">
    	                	<div class="suggestion_image">
    	                		<div class="ratio-1-1">
    								<img src="{$url_img}" alt="{$product.name}" class="img-fluid">
    							</div>
    						</div>
    	                	<div class="suggestion_content mt-3">
        	                	{assign var = data_categories value = $this->Product->getDetailProduct($product.id, {LANGUAGE}, [
                                   'get_categories' => true
                                 ])}
    	                	    {if !empty($data_categories.categories)}
                                    {foreach from = $data_categories.categories item = category}
                                        {if !empty($category.name)}
                                            <a class="mb-2 d-inline-block text-secondary" href="{$this->Utilities->checkInternalUrl($category.url)}">
                                                {if $category@first}
                                                    {$category.name|escape|truncate:40:" "}
                                                {/if}
                                            </a>
                                        {/if}
                                    {/foreach}
                                {/if}
    	                		{if !empty($product.name)}
    	                    		<h5 class="color-main">
    	                    			{$product.name|truncate:35:" "}
    	                    		</h5>
    	                		{/if}
    	            			<div class="price suggest-pricen">
                                    {if !empty($product.price)}                        
                                        <span class="price-amount fs-14 color-main">
                                            {if empty($product.apply_special) && !empty($product.price)}
                                                {$product.price|number_format:0:".":","}
                                                <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                            {/if}
                        
                                            {if !empty($product.apply_special) && !empty($product.price_special)}
                                                {$product.price_special|number_format:0:".":","}
                                                <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                            {/if}
                                        </span>  
                                    
                                    
                                        {if !empty($product.apply_special) && !empty($product.price)}
                                            <span class="price-amount old-price fs-14">
                                                {$product.price|number_format:0:".":","}
                                                <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                            </span>
                                        {/if}
                                    {else}
                                        <span class="price-amount fs-14">
                                            {__d('template', 'lien_he')}
                                        </span>  
                                    {/if}
    	            			</div>
    	                	</div>
    	                </a>
    	            </li>
	            {/if}
	        {/foreach}
	    </div>
	{/if}
</div>
