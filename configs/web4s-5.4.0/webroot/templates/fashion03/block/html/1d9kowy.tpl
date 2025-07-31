{strip}{assign website_info value = $this->Setting->getWebsiteInfo()}

<div class="entire-info-website text-white">
    
    {if !empty($website_info.company_name)}
        <div class="title-footer text-uppercase mb-4">
            {$website_info.company_name}
        </div>
    {/if}
    
    {if !empty($data_extend['locale'][{LANGUAGE}]['mo_ta'])}
        <div class="descript-website-section">
            <p>
                {$this->Block->getLocale('mo_ta', $data_extend)}
            </p>
        </div>
    {/if}
    
    <address>
        {if !empty($website_info.address)}
            <p>
                <i class="fa-solid fa-building"></i>
                {$website_info.address}
            </p>
        {/if}
        
        {if !empty($website_info.phone)}
            <p>
                <i class="fa-solid fa-phone"></i>
                {$website_info.phone}
            </p>
        {/if}
        
        {if !empty($website_info.email)}
            <p>
                <i class="fa-solid fa-envelope"></i>
                {$website_info.email}
            </p>
        {/if}
    </address>
</div>{/strip}