{strip}{assign icons value = $this->Block->getLocale('icons', $data_extend)}
		
{if !empty($icons)}
	<div id="counter" class="row w-develope">
		{foreach from = $icons item = icon}
			<div class="col-6 col-md-3">					
				<div class="content-counter">
					<div class="info-counter text-center">
						<div class="text_tktc number">
							<div class="po_text">
							    {if !empty($icon.number)}		
									<span class="counter-value" data-count="{$icon.number}">0</span>											
								{/if}
								{if !empty($icon.dvt)}	
									<span>{$icon.dvt}</span>
								{/if}
							</div>
						</div>							
						
						<p class="name-count">
							{if !empty($icon.name)}
								{$icon.name}
							{/if}
						</p>
					</div>
				</div>					
			</div>
		{/foreach}				
	</div>
{/if}{/strip}