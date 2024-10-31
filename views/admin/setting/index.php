<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php ovoform_admin_top($pageTitle) ?>
<div class="row">
    <div class="col-12">
        <div class="card custom--card">
            <form action="<?php echo esc_url(ovoform_route_link('admin.extension.update',pageName:'ovoform_settings')); ?>" method="post">
                <?php ovoform_nonce_field('admin.extension.update'); ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label class="form-label"><?php esc_html_e('Site Key', 'ovoform'); ?></label>
                                <input type="text" class="form-control form--control" name="site_key" value="<?php echo esc_attr($shortcode->site_key->value); ?>" required>
                                <small><i class="las la-info-circle"></i> <?php esc_html_e('Google reCaptcha site key', 'ovoform'); ?></small>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?php esc_html_e('Secret Key', 'ovoform'); ?></label>
                                <input type="text" class="form-control form--control" name="secret_key" value="<?php echo esc_attr($shortcode->secret_key->value); ?>" required>
                                <small><i class="las la-info-circle"></i> <?php esc_html_e('Google reCaptcha secret key', 'ovoform'); ?></small>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn--primary"> <i class="las la-check-circle"></i> <?php esc_html_e('Save', 'ovoform'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php ovoform_admin_bottom() ?>