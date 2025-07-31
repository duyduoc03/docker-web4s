{strip}<div class="footer-menu-section">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <div class="title-footer text-uppercase mb-4">
            {$this->Block->getLocale('tieu_de', $data_extend)}
        </div>
    {/if}
    
    <div class="embed-responsive embed-responsive-4by3">
        <iframe nh-lazy="iframe" class="embed-responsive-item" data-src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fvinfastphantrongtue.hn&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=439222267533096" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        
    </div>
</div>{/strip}