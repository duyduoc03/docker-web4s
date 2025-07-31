{strip}{assign website_info value = $this->Setting->getWebsiteInfo()}

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
</div>{/strip}