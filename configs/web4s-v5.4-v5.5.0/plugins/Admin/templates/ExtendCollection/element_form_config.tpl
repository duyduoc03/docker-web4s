<div class="row" data-sticky-container>
    <div class="col-xl-9 col-lg-9">
        <div id="wrap-structure-template" class="clearfix">
            <div class="kt-portlet nh-template-portlet">
                <div class="kt-portlet__head p-10">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'cau_hinh')}
                        </h3>
                    </div>

                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                            <span class="btn btn-sm btn-icon btn-secondary bg-white btn-icon-md m-0 nh-btn-toggle-row d-none" data-toggle="kt-tooltip" data-placement="top" title="{__d('admin', 'dong_mo')}">
                                <i class="la la-angle-down"></i>
                            </span>
                        
                            <span class="btn btn-sm btn-success btn-icon-md m-0 nh-btn-add-row" data-toggle="kt-tooltip" data-placement="top" title="{__d('admin', 'them_dong_moi')}" >
                                <i class="la la-plus"></i>
                                {__d('admin', 'them_dong')}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet__body p-0">
                    <div class="kt-portlet__content p-10">
                        <div class="row">
                            <div class="col-12 wrap-list-item">
                                {if !empty($collection_info.form_config.rows)}
                                    {foreach from = $collection_info.form_config.rows item = row}
                                        {if !empty($row.columns)}
                                            <div data-code="{if !empty($row.code)}{$row.code}{/if}" data-config="{if !empty($row.config)}{htmlentities($row.config|@json_encode)}{/if}" class="row nh-row-item">
                                                {foreach from = $row.columns item = column}                                               
                                                    {assign var = column_value value = "{if !empty($column.column_value)}{$column.column_value}{/if}"}
                                                    <div data-column-value="{$column_value}" class="nh-column-item col-{$column_value}">
                                                        <div class="nh-content-column">
                                                            {if !empty($column.field)}
                                                                {foreach from = $column.field item = field}
                                                                    {if !empty($field)}
                                                                        <li data-code="{if !empty($field.code)}{$field.code}{/if}" class="field-item">
                                                                            <i class="fa fa-file-alt w-20px fs-14"></i>
                                                                            <span>
                                                                                {if !empty($field.name)}{$field.name}{/if}
                                                                            </span>
                                                                        </li>
                                                                    {/if}
                                                                {/foreach}
                                                            {/if}
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3">
        <div class="kt-portlet nh-portlet sticky" data-sticky="true" data-margin-top="140px" data-sticky-class="kt-sticky">
            <div class="kt-portlet__body p-10 border">
                <div class="row">
                    <div class="col-12">
                        {if !empty($collection_info.fields)}
                            <div class="clearfix mb-10">
                                <span class="fw-400 mb-10 fs-16">
                                    {__d('admin', 'truong_du_lieu')}
                                </span>
                            </div>
                        {/if}

                        <div class="nh-list-block">
                            <ul>    
                                {if !empty($collection_info.fields)}
                                    {foreach from = $collection_info.fields item = item}
                                        <li data-code="{if !empty($item.code)}{$item.code}{/if}" class="field-item">
                                            <i class="fa fa-file-alt w-20px fs-14"></i>
                                            <span>
                                                {if !empty($item.name)}
                                                    {$item.name}
                                                {/if}
                                            </span>
                                        </li>
                                    {/foreach}
                                {else}
                                    <span class="fs-12 fw-400">
                                        {__d('admin', 'chua_co_thong_tin')}.
                                    </span>
                                {/if}
                            </ul>
                        </div>   
                    </div>                        
                </div>
            </div>
        </div>            
    </div>
</div>

<div class="d-none">                    
    <input type="hidden" name="config" value="">
</div>