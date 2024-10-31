<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
    $html = '<button type="button" class="btn btn--primary form-generate-btn"> <i class="la la-fw la-plus"></i> Add Form</button>';
    ovoform_admin_top($pageTitle,$html);
?>

<form action="<?php echo esc_url(ovoform_route_link('admin.forms.store',pageName:'ovoform_forms')); ?>" method="post">
    <?php ovoform_nonce_field('admin.forms.store') ?>
    <div class="col-lg-12">
        <div class="card p-0">
            <div class="card-header"><h5 class="mb-0"><?php esc_html_e('User Data', 'ovoform'); ?></h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Name', 'ovoform') ?></label>
                    <input type="text" class="form-control form--control" name="name" value="<?php echo esc_attr(ovoform_old('name')); ?>" required>
                </div>
               
                <div class="form-group form--switch pb-2">
                    <div class="form-check form-switch p-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="ovoform_google_recaptcha" name="ovoform_google_recaptcha">
                        <label class="form-check-label fw-bold ms-2" for="ovoform_google_recaptcha"><?php esc_html_e('Enable / Disable reCaptcha', 'ovoform'); ?></label>
                    </div>
                </div>
                <div class="row addedField">
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn--primary"><?php esc_html_e('Submit', 'ovoform'); ?></button>
            </div>
        </div>
    </div>
</form>


<?php ovoform_include('form/modal') ?>
<?php ovoform_include('form/generator') ?>

<script>
    "use strict";
    var formGenerator = new FormGenerator();
</script>

<?php ovoform_include('form/action') ?>
<?php ovoform_admin_bottom(); ?>