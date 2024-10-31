<?php if ( ! defined( 'ABSPATH' ) ) exit;

$html = '<button type="button" class="btn btn--primary form-generate-btn"> <i class="la la-fw la-plus"></i> Add Form</button>';
ovoform_admin_top($pageTitle, $html);

?>

<form action="<?php echo esc_url(ovoform_route_link('admin.forms.update',pageName:'ovoform_forms')); ?>" method="post">
    <?php ovoform_nonce_field('admin.forms.update') ?>
    <input type="hidden" class="form-control" name="id" value="<?php echo esc_attr($formInfo->id); ?>">
    <div class="col-lg-12">
        <div class="card p-0 mb-3">
            <div class="card-body">
                <p class="fw-bold mb-1"><?php esc_html_e('Copy Shortcode','ovoform') ?></p>
                <span class="copy-element">[<?php echo esc_html__('ovoform', 'ovoform'); ?>-<?php esc_html_e('form id', 'ovoform'); ?>="<?php echo esc_html(intval($formInfo->id)); ?>" <?php esc_html_e('title', 'ovoform'); ?>="<?php printf(esc_html__('%s', 'ovoform'), esc_html($formInfo->name)); ?>"]</span>
            </div>
        </div>
        <div class="card p-0">
            <div class="card-header"><h5 class="mb-0"><?php esc_html_e('User Data', 'ovoform'); ?></h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Name', 'ovoform') ?></label>
                    <input type="text" class="form-control form--control" name="name" value="<?php echo esc_attr($formInfo->name); ?>" required>
                </div>
                <div class="form-group form--switch pb-2">
                    <div class="form-check form-switch p-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="ovoform_google_recaptcha" name="ovoform_google_recaptcha" <?php if($formInfo->captcha_required){ ?> checked <?php } ?>>
                        <label class="form-check-label fw-bold ms-2" for="ovoform_google_recaptcha"><?php esc_html_e('Enable / Disable reCaptcha', 'ovoform'); ?></label>
                    </div>
                </div> 
                <div class="row addedField">
                    <?php if (!ovoform_check_empty($form)) {
                        $form->form_data = json_decode(json_encode(maybe_unserialize($form->form_data)));
                        foreach ($form->form_data as $key => $formData) {
                    ?>
                            <div class="col-md-4">
                                <div class="card border mb-3" id="<?php echo esc_attr($key); ?>">
                                    <input type="hidden" name="form_generator[is_required][]" value="<?php echo esc_attr($formData->is_required); ?>">
                                    <input type="hidden" name="form_generator[extensions][]" value="<?php echo esc_attr($formData->extensions); ?>">
                                    <input type="hidden" name="form_generator[options][]" value="<?php echo esc_attr(implode(',', $formData->options)); ?>">

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label><?php esc_html_e('Label', 'ovoform'); ?></label>
                                            <input type="text" name="form_generator[form_label][]" class="form-control" value="<?php echo esc_attr($formData->name); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label><?php esc_html_e('Type', 'ovoform'); ?></label>
                                            <input type="text" name="form_generator[form_type][]" class="form-control" value="<?php echo esc_attr($formData->type); ?>" readonly>
                                        </div>
                                        <?php

                                        //Show
                                        $jsonData = [
                                            'type' => $formData->type,
                                            'is_required' => $formData->is_required,
                                            'label' => $formData->name,
                                            'extensions' => explode(',', $formData->extensions) ?? 'null',
                                            'options' => $formData->options,
                                            'old_id' => '',
                                        ];
                                        ?>

                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn--primary editFormData" data-form_item='<?php echo wp_json_encode($jsonData); ?>' data-update_id="<?php echo esc_attr($key); ?>">
                                                <i class="las la-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn--danger removeFormData"><i class="las la-times"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    <?php }
                    } ?>
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
