<?php
if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_style('iziToast', ovoform_assets('global/css/iziToast.min.css'));
wp_enqueue_script('iziToast', ovoform_assets('global/js/iziToast.min.js'), array('jquery'), null, true);
?>


<?php if (ovoform_session()->has('errors')) {
        foreach (ovoform_session()->get('errors') as $msg) { ?>
            <script>
            jQuery(document).ready(function($) {
                "use strict";
                iziToast["error"]({
                    message: "<?php echo esc_html($msg) ?>",
                    position: "topRight"
                });
            });
        </script>
        <?php } ?>
    <?php } ?>

<?php if (ovoform_session()->has('notify')) {
    foreach (ovoform_session()->get('notify') as $msg) { ?>
        <script>
            jQuery(document).ready(function($) {
                "use strict";
                iziToast["<?php echo esc_html($msg[0]) ?>"]({
                    message: "<?php echo esc_html($msg[1]) ?>",
                    position: "topRight"
                });
            });
        </script>
    <?php } ?>
<?php } ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        function notify(status, message) {
            iziToast[status]({
                message: message,
                position: "topRight"
            });
        }
    });
</script>