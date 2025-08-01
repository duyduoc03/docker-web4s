{if !empty($schema_data[{BRAND}])}
    {assign url_website value = $this->Utilities->getUrlWebsite()}
    {assign website_info value = $this->Setting->getWebsiteInfo()}
    {assign brand value = $schema_data[{BRAND}]}

    {assign image_avatar value = ''}
    {if !empty($brand.image_avatar)}
        {$image_avatar = "{CDN_URL}{$brand.image_avatar}"}
    {/if}

    {assign schema_brand value = [
        '@context' => 'https://schema.org',
        '@type' => 'Brand',
        'name' => "{if !empty($brand.name)}{$brand.name}{/if}",
        'logo' => $image_avatar,
        'description' => "{if !empty($brand.content)}{$brand.content|strip_tags}{/if}",
        'url' => "{$url_website}/{if !empty($brand.url)}{$brand.url}{/if}",
        'mainEntityOfPage' => $this->Utilities->getUrlCurrent(),
    ]}

    <script type="application/ld+json">
        {$schema_brand|@json_encode}
    </script>
{/if}