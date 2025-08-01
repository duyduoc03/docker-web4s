
<form nh-form="list-template" action="{ADMIN_PATH}/setting/get-list-locale-label" method="POST" autocomplete="off" class="h-100">
    <div class="kt-list-locales">
        {$this->element('../SettingLocale/search_advanced')}
        
        <div nh-form-table="table-locales-brand" >			
        </div>
    </div>
</form>