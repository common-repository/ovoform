<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<!-- Deactivate modal -->
<div class="ovoform-admin">
  <div class="modal ovoform-deactivate-modal" id="ovoformDeactivateModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <button type="button" class="modal-close-btn text--danger" data-bs-dismiss="modal"> <i class="fas fa-times"></i> </button>
        <div class="ovoform-dm-head">
          <h1 class="mb-3"><?php esc_html_e('Are you sure you want to deactivate?', 'ovoform') ?></h1>
          <p class="fs-20"><?php esc_html_e('Before you go, please share your feedback to us that why you\'re deactivating the plugin.', 'ovoform') ?></p>
        </div>
        <div class="modal-body">
          <form class="ovoform-deactivate-form">
            <div class="form-check">
              <input class="form-check-input" type="radio" checked name="deactivate_reason" value="temporary" id="temporary">
              <label class="form-check-label" for="temporary"><?php esc_html_e('Temporary deactivation', 'ovoform');?></label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="deactivate_reason" value="site_slow" id="site_slow">
              <label class="form-check-label" for="site_slow"><?php esc_html_e('It slowed down my site', 'ovoform');?></label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="deactivate_reason" value="bugs" id="bugs">
              <label class="form-check-label" for="bugs"><?php esc_html_e('It\'s buggy', 'ovoform');?></label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="deactivate_reason" value="found_better" id="found_better">
              <label class="form-check-label" for="found_better"><?php esc_html_e('I found a better plugin', 'ovoform');?></label>
              <input type="text" class="form-control form--control d-none reason_input mt-3" placeholder="<?php esc_attr_e('Please, share the plugin name','ovoform') ?>" name="reason">
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="deactivate_reason" value="no_needs" id="no_needs">
              <label class="form-check-label" for="no_needs"><?php esc_html_e('I found a better plugin', 'ovoform');?></label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="deactivate_reason" value="others" id="others">
              <label class="form-check-label" for="others"><?php esc_html_e('I found a better plugin', 'ovoform');?></label>
              <input type="text" class="form-control form--control d-none reason_input mt-3" placeholder="<?php esc_attr_e('Please, share the reason','ovoform') ?>" name="reason">
            </div>
            <div class="d-flex justify-content-between ovoform-dm-footer">
              <a href="#" class="ovoform-skip-btn"><?php esc_html_e('Skip & Deactivate', 'ovoform');?></a>
              <button type="submit" class="btn btn--primary ovoform-submit-reason-btn"><?php esc_html_e('Submit & Deactivate', 'ovoform');?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Deactivate modal -->