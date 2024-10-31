<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="d-flex mb-15 justify-content-between gap-3 align-items-center">
    <h4 class="mb-0"><?php printf(esc_html__('%s', 'ovoform'), esc_html(isset($pageTitle) ? $pageTitle : 'Dashboard') ); ?></h4>
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <?php if($html) echo wp_kses_post($html); ?>
    </div>
</div>