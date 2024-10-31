<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Confirmation Alert!', 'ovoform');?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
            <input type="hidden" name="nonce">
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php esc_html_e('No', 'ovoform');?></button>
                    <button type="submit" class="btn btn-primary"><?php esc_html_e('Yes', 'ovoform');?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($){
        "use strict";
        $(document).on('click','.confirmationBtn', function () {
            var modal   = $('#confirmationModal');
            let data    = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.find('[name=nonce]').val(data.nonce);
            modal.modal('show');
        });
    });
</script>