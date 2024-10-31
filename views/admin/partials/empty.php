<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="ovoform-empty text-center">
    <div class="empty-icon">
        <img src="<?php echo esc_url(ovoform_get_image(ovoform_assets('images/empty.png'))); ?>" alt="">
    </div>
    <div class="empty-text">
        <h4 class="text-muted"><?php printf(esc_html__('%s', 'ovoform'),  esc_html($title)); ?></h4>
    </div>
    <?php if(isset($button_url)): ?>
    <div class="empty-button">
        <a href="<?php echo esc_url($button_url) ?>" class="btn btn--primary"><i class="las la-plus-circle"></i> <?php esc_html_e('Generate a Form', 'ovoform'); ?></a>
    </div>
    <?php endif ?>
</div>