{assign var = keyword value = $this->Utilities->getParamsByKey('keyword')}
{assign var = status value = $this->Utilities->getParamsByKey('status')}
{assign var = create_from value = $this->Utilities->getParamsByKey('create_from')}
{assign var = create_to value = $this->Utilities->getParamsByKey('create_to')}

<div list-comment="{ARTICLE}" data-type="{ARTICLE_DETAIL}">
	<div class="row">
		<div class="col-lg-6">
			<div class="kt-portlet kt-portlet--height-fluid hide-box-shadow">
				<div class="kt-portlet__body kt-portlet__body--fit">
					<div class="kt-widget kt-widget--project-1 p-0 m-0 border rounded">
						<div class="kt-widget__head pb-0">
							<div class="kt-widget__label">
								<div class="kt-widget__info p-0 m-0">
									<div class="kt-widget__title">
										{__d('admin', 'binh_luan')}
									</div>
								</div>
							</div>
							<div class="kt-portlet__head-toolbar d-flex align-items-center">
								{if !empty($number_comment)}
									<div class="count mr-3">
										<i class="flaticon2-talk text-warning"></i>
										<span class="kt-font-bold"> 
											{$number_comment} 
											{__d('admin', 'binh_luan')}
										</span>
									</div>
									<a href="javascript://" data-toggle="modal" data-target="#modal-filter-comment" btn-filter data-type="comment" class="px-2 py-1 border rounded">
										<i class="fa fa-filter"></i>
										<span class="kt-font-brand kt-font-bold">
											{__d('admin', 'bo_loc')}
										</span>
									</a>
								{/if}
							</div>
						</div>

						<div class="kt-widget__body">
							<div nh-comment="{if !empty($id)}{$id}{/if}" class="comment-section1 kt-notes mt-15">
							    <ul nh-list-comment="" class="list-comment1 kt-notes__items">
							    	{if empty($number_comment)}
							    		<i>{__d('admin', 'bai_viet_chua_co_binh_luan_nao')}</i>
							    	{/if}
							    </ul>
							</div>
						</div>
					</div>
				</div>	
			</div>
		</div>

		<div class="col-lg-6">
			<div class="kt-portlet kt-portlet--height-fluid hide-box-shadow">
				<div class="kt-portlet__body kt-portlet__body--fit">
					<div class="kt-widget kt-widget--project-1 p-0 m-0 border rounded">
						<div class="kt-widget__head pb-0">
							<div class="kt-widget__label">
								<div class="kt-widget__info p-0 m-0">
									<div class="kt-widget__title">
										{__d('admin', 'danh_gia')}
									</div>
								</div>
							</div>
							<div class="kt-portlet__head-toolbar d-flex align-items-center">
								{if !empty($number_rating)}
									<div class="count mr-3">
										<i class="flaticon-star text-warning"></i>
										<span class="kt-font-bold">
											{$number_rating} 
											{__d('admin', 'danh_gia')}
										</span>
									</div>
									<a href="javascript://" data-toggle="modal" data-target="#modal-filter-comment" btn-filter data-type="rating" class="px-2 py-1 border rounded">
										<i class="fa fa-filter"></i>
										<span class="kt-font-brand kt-font-bold">
											{__d('admin', 'bo_loc')}
										</span>
									</a>
								{/if}
							</div>
						</div>

						<div class="kt-widget__body">
							<div nh-rating="{if !empty($id)}{$id}{/if}" class="mt-15">
							    <ul nh-list-rating="" class="list-rating">
							    	{if empty($number_rating)}
							    		<i>{__d('admin', 'bai_viet_chua_co_danh_gia_nao')}</i>
							    	{/if}
							    </ul>
							</div>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>
