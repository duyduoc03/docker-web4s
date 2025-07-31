{assign var = url_website  value = $this->UtilitiesAdmin->getUrlWebsite()}

 <div class="wrap-item">
     <div class="row">  
        
    
        <div class="col-lg-6">
                <label>
                        {__d('admin', 'duong_dan_file')}
                </label>
                <div class="form-group">
                    <div class="input-group">  
                        <div class="input-group-prepend">
                            <span class="bg-grey btn cursor-default">{if !empty($url_website)}{$url_website}{/if}</span>
                        </div>       
                        <input  id="linkFile" name="file[]" value="{if !empty($item)}{$item}{/if}" class="form-control" type="text">
                    </div>
                    
                </div>
        </div>

            <div class="form-group">
                <div class="btn-delete-item-new">  
            <i class="fa fa-trash-alt btn btn-secondary btn-sm"></i>
                </div>  
            </div>
            {if !empty($item)}
            <div class="form-group">
                <a  id="tab-link-file" href="javascript:;" > 
                    <div class="btn-open-link"> 
                        <span class="btn btn-secondary btn-sm">
                        <i class="fa fa-link "></i>
                        </span>
                    </div>  
                </a>
            </div>
            {/if}
     </div>
</div>
