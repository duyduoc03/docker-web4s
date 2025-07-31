<div class="kt-wizard-v1__nav-items kt-wizard-v1__nav-items--clickable">
    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" {if empty($step) || $step == 1}data-ktwizard-state="current"{/if}>
        <div class="kt-wizard-v1__nav-body">
            <div class="kt-wizard-v1__nav-icon">
                <i class="flaticon-file-1"></i>
            </div>
            <div class="kt-wizard-v1__nav-label">
                1. {__d('admin', 'bang_du_lieu')}
            </div>
        </div>
    </div>
    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" {if !empty($step) && $step == 2}data-ktwizard-state="current"{/if}>
        <div class="kt-wizard-v1__nav-body">
            <div class="kt-wizard-v1__nav-icon">
                <i class="flaticon-list-1"></i>
            </div>
            <div class="kt-wizard-v1__nav-label">
                2. {__d('admin', 'truong_du_lieu')}
            </div>
        </div>
    </div>
    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" {if !empty($step) && $step == 2}data-ktwizard-state="current"{/if}>
        <div class="kt-wizard-v1__nav-body">
            <div class="kt-wizard-v1__nav-icon">
                <i class="flaticon-settings-1"></i>
            </div>
            <div class="kt-wizard-v1__nav-label">
                3. {__d('admin', 'form_nhap_lieu')}
            </div>
        </div>
    </div>
</div>