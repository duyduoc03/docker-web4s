<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__stats m-0 w-100 text-center">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item box-shadow-0 cursor-default p-0">
                    <div class="kt-portlet__head kt-portlet__space-x">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'lien_he_moi_nhat')}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="/admin/contact" target="_blank">
                                        {__d('admin', 'xem_tat_ca')}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                {if !empty($list_contact)}
                    <div class="kt-portlet__body comment_dashboard-over-flow contact-dashboard ">
                        <div class="tab-content">
                            <div class="tab-pane active" id="kt_widget5_tab1_content" aria-expanded="true">
                                <div class="kt-widget5">
                                {foreach from = $list_contact key = key item = item_contact}
                                    {assign var = status value = "{if !empty($item_contact.status) && $item_contact.status != 2}{__d('admin', 'da_doc')}{else}{__d('admin', 'chua_doc')}{/if}"}
                                    <div class="kt-widget5__item pb-3 row">
                                        <div class="kt-widget5__content text-left col-md-7">
                                            <div class="kt-widget5__section">
                                                {if !empty($item_contact.name_form)}
                                                    <div class="kt-widget5__title">
                                                        {$item_contact.name_form}
                                                    </div>
                                                {/if}
                                                
                                                {if !empty($item_contact.values)}
                                                    {foreach from = $item_contact.values key = key item = item}
                                                        {if !empty($item.field_value)}    
                                                            <div class="kt-widget5__info text-justify">
                                                                {if !empty($item.label)}
                                                                    <span>{$item.label}:</span>
                                                                {/if}
                                                                    <span class="">{strip_tags($item.field_value)}</span>                   
                                                            </div>
                                                        {/if}
                                                    {/foreach}
                                                    {if !empty($item_contact.id)}
                                                        <a class="kt-widget3__text mb-2 d-inline-block cursor-p mt-2" href="/admin/contact/#detail-contact={$item_contact.id}" target="_blank">
                                                            <i class="fa fa-external-link-alt"></i> {__d('admin', 'chi_tiet')}
                                                        </a>
                                                    {/if} 
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="col-md-5 text-right">
                                            <div class="kt-widget5__content justify-content-end mb-1">
                                                <div class="kt-widget5__stats pr-3">
                                                    {if !empty($item_contact.tracking_source)}
                                                        <span class="kt-widget5__number fw-600">{__d('admin', {strip_tags($item_contact.tracking_source)})}</span>
                                                    {/if}
                                                    <span class="kt-widget5__sales">{__d('admin', 'nguon')}</span>
                                                </div>
                                                <div class="kt-widget5__stats">
                                                    {if !empty($item_contact.status)}
                                                        <span class="kt-widget5__number fw-600">{$status}</span>
                                                    {/if}
                                                    <span class="kt-widget5__votes">{__d('admin', 'trang_thai')}</span>
                                                </div>
                                            </div>
                                            {if !empty($item_contact.created)}
                                                <p class="fs-12 font-italic mb-2 text-right">
                                                    {$item_contact.created}
                                                </p>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>   
                {/if}
                </div>
            </div>
        </div>
    </div>
{if empty($list_contact)}
    <div class="kt-portlet__body text-center">
        <div class="kt-font-bolder">
            {__d('admin', 'khong_co_lien_he_moi')}
        </div>
    </div>
{/if}
</div>