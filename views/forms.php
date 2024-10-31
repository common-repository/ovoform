<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

$formInfo = \Ovoform\Models\FormInfo::findOrFail($attributes['id']);
?>
<div class="ovoform-card">
    <div class="card-body">

        <div class="alerts">

            <?php if (ovoform_session()->has('errors')) {
                foreach (ovoform_session()->get('errors') as $msg) { ?>
                    <div class="alert alert-danger">
                        <?php echo esc_html($msg) ?>
                    </div>
            <?php }
            } ?>

            <?php if (ovoform_session()->has('notify')) {
                foreach (ovoform_session()->get('notify') as $msg) { ?>
                    <div class="alert alert-<?php echo esc_html($msg[0]) ?>">
                        <?php echo esc_html($msg[1]) ?>
                    </div>

                <?php } ?>
            <?php } ?>
        </div>

        <form class="verify-gcaptcha" action="<?php echo esc_url(ovoform_route_link('form.submit')); ?>" method="post" enctype="multipart/form-data">
            <?php ovoform_nonce_field('form.submit') ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($attributes['id']); ?>">
            <?php
            echo wp_kses_post(ovoform_get_form($formInfo->form_id));

            if ($formInfo->captcha_required) {
                ovoform_include('partials/captcha');
            }
            ?>
            <div class="btn-area">
                <button type="submit" class="btn"><?php esc_html_e('Submit', 'ovoform'); ?></button>
            </div>
        </form>
    </div>
</div>