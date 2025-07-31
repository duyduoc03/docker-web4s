{strip}
{assign var = form_url value = $this->Block->getLocale('duong_dan_tim_kiem', $data_extend)}
<div class="entire-action-header px-4">
  <a class="btn-action-header quick_btn" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    <i class="fa-light fa-magnifying-glass"></i>
  </a>
</div>
<div class="collapse quick_search_content" id="collapseExample">
    <div class="container">
        <div class="card card-body">
            <div class="d-flex justify-content-between mb-5">
                {assign website_info value = $this->Setting->getWebsiteInfo()}
                <div class="logo-section">
                    {if !empty($website_info.company_logo)}
                        <a href="/">
                            {$this->LazyLoad->renderImage([
                                'src' => "{CDN_URL}{$website_info.company_logo}", 
                                'alt' => "logo",
                                'class' => 'img-fluid',
                                'ignore' => true
                            ])}
                        </a>
                    {/if}
                </div>
                <div class="text-right">
                    <a class="btn-action-header quick_btn btn_close close-sidebar effect-rotate icon-close" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa-light fa-xmark"></i>
                    </a>
                </div>
            </div>
            <div class="quick_search">
                <div class="search_form">
                    <form action="{$form_url}" method="get" autocomplete="off">
                        <div class="search-popup__input">
                            <input nh-auto-suggest="{ALL}" name="keyword" placeholder="{__d('template', 'tu_khoa_tim_kiem')}..." type="text" value="{$this->Utilities->getParamsByKey('keyword')}">
                            <button nh-btn-submit class="btn button-popup__input" type="submit">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{/strip}