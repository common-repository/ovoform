<?php
if (!defined('ABSPATH')) exit;

$googleCaptcha = ovoform_re_captcha();
?>

<?php if ($googleCaptcha) : ?>
    <div class="mb-3">
        <?php echo wp_kses($googleCaptcha, array(
            'script' => array(
                'src' => array(),
            ),
            'div' => array(
                'class' => array(),
                'data-sitekey' => array(),
                'data-callback' => array(),
                'id' => array(),
            ),
        )); ?>
    </div>
<?php endif ?>

<script>
    jQuery(document).ready(function($) {
        "use strict"
        $('.verify-gcaptcha').on('submit', function() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">Captcha field is required.</span>';
                return false;
            }
            return true;
        });
    });
</script>