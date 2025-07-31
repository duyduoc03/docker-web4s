{assign var = product value = []}
{if !empty($data_block.data)}
	{assign var = product value = $data_block.data}
{/if}

{if !empty($product)}
	{strip}
	
	<div class="box-product-detail" style="background-image: url({CDN_URL}/media/slider/vehicle_background.webp);">
	    <div class="container">
	        <div class="product-detail-head" >
        		<div class="row">
        			<div class="col-12 col-lg-7">
        				{$this->element("../block/product_detail/element_product_image", [
        	                'product' => $product
        	            ])}
        			</div>
        
        			<div class="col-12 col-lg-5">
        				{$this->element("../block/product_detail/element_product_info", [
        	                'product' => $product
        	            ])}
        			</div>
        		</div>
        	</div>
	    </div>
	</div>
{else}
	<p class="text-center font-danger my-4">{__d('template', 'thong_tin_san_pham_khong_ton_tai')}</p>
{/if}