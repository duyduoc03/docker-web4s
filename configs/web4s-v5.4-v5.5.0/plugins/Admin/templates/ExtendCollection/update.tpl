<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/extend-collection" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
            
            {$this->element('Admin.page/language')}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__body kt-portlet__body--fit">

            {assign var = state_kwizard value = 'first'}
            {if !empty($step) && $step == 2}
                {$state_kwizard = 'between'}
            {/if}

            {if !empty($step) && $step == 3}
                {$state_kwizard = 'last'}
            {/if}

            <div id="wizard-extent-collection" data-step="{if !empty($step)}{$step}{/if}" class="kt-grid kt-wizard-v1 kt-wizard-v1--white" data-ktwizard-state="{$state_kwizard}">
                <div class="kt-grid__item">
                    <div class="kt-wizard-v1__nav">
                        {$this->element('../ExtendCollection/element_step')}
                    </div>
                </div>

                <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v1__wrapper">
                    <div id="kt-form-wizard" class="kt-form" extend-id="{if !empty($id)}{$id}{/if}">
                        <form id="collection-form" action="{ADMIN_PATH}/extend-collection/save{if !empty($id)}/{$id}{/if}">
                            <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" {if empty($step) || $step == 1}data-ktwizard-state="current"{/if}>
                                <div class="kt-heading kt-heading--md">
                                    {__d('admin', 'bang_du_lieu')}
                                </div>

                                <div class="kt-form__section kt-form__section--first">
                                    {$this->element('../ExtendCollection/element_basic_info')}
                                </div>                                
                            </div>

                            <div wrap-manager="fields" class="kt-wizard-v1__content" data-ktwizard-type="step-content" {if !empty($step) && $step == 2}data-ktwizard-state="current"{/if}>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="kt-heading kt-heading--md">
                                        {__d('admin', 'truong_du_lieu')}
                                    </div>
                                    <span id="add-field-extend" class="btn btn-sm btn-success">
                                        <i class="fa fa-plus"></i>
                                        {__d('admin', 'them_truong')}
                                    </span>
                                </div>
                                
                                {$this->element('../ExtendCollection/element_fields')}                            
                      
                            </div>
                        </form>                        

                        {if !empty($id)}
                            <form id="structure-form" action="{ADMIN_PATH}/extend-collection/save-form-config/{$id}">
                                <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" {if !empty($step) && $step == 3}data-ktwizard-state="current"{/if}>
                                    <div class="kt-heading kt-heading--md">
                                        {__d('admin', 'form_nhap_lieu')}
                                    </div>
                                    <div class="kt-container--fluid kt-grid__item kt-grid__item--fluid">
                                        {$this->element('../ExtendCollection/element_form_config')}
                                    </div>                                    
                                </div>
                            </form>
                        {/if}

                        <div class="kt-form__actions">
                            <span data-ktwizard-type="action-prev" class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold d-none">
                                {__d('admin', 'quay_lai')}
                            </span>

                            <span data-ktwizard-type="action-next"class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold d-none">
                                {__d('admin', 'tiep_tuc')}
                            </span>

                            <span nh-btn="save-structure" class="btn btn-success btn-md btn-tall btn-wide kt-font-bold d-none">
                                {__d('admin', 'cap_nhat')}
                            </span>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

{if !empty($id)}
    {$this->element('../ExtendCollection/popover_setting_row')}
    {$this->element('../ExtendCollection/modal_row_setting_general')}
    {$this->element('../ExtendCollection/modal_row_setting_column')}
{/if}