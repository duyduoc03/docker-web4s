{strip}<div class="newsletter" nh-lazy="image-background" data-src="{CDN_URL}/media/tin-tuc/240.webp" >
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-5">
                {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
                    <div class="h2 mb-4 text-white">
                        {$this->Block->getLocale('tieu_de', $data_extend)}
                    </div>
                {/if}
                
                <form nh-form-contact="I5HX0D12MT" action="/contact/send-info" method="POST" autocomplete="off" class=" space-block-5">
                    <div class="row">
                        <div class="col-lg-8 col-7">
                            <div class="form-group mb-0">
                                <input required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" 
                                    data-rule-maxlength="255" data-msg-maxlength="{__d('template', 'thong_tin_nhap_qua_dai')}" 
                                    name="email" type="email" class="form-control newsletter--input" placeholder="{__d('template', 'email')}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-5">
                            <div class="form-group mb-0">
                                <span nh-btn-action="submit" class="btn newsletter--submit">
                                    {__d('template', 'gui_nhan_tin')}
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-7">
                <div class="newsletter-car">
                    {$this->LazyLoad->renderImage([
                		'src' => "{if !empty($data_extend['locale'][{LANGUAGE}]['image'])}{$this->Utilities->replaceVariableSystem($this->Block->getLocale('image', $data_extend))}{/if}", 
                		'alt' => "{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}{$this->Block->getLocale('tieu_de', $data_extend)}{/if}",
                		'class' => 'img-fluid'
                	])}
                </div>
            </div>
        </div>
    </div>
</div>{/strip}