<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="modal" id="formGenerateModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php esc_html_e('Generate Form', 'ovoform');?></h5>
          <button type="button" class="close bg-danger rounded text-white" data-bs-dismiss="modal" aria-label="Close">
              <i class="las la-times"></i>
          </button>
        </div>
        <form class="generate-form">
              <div class="modal-body">
                <input type="hidden" name="update_id" value="">
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Form Type', 'ovoform');?></label>
                    <select name="form_type" class="form-control form--control" required>
                        <option value=""><?php esc_html_e('Select One', 'ovoform');?></option>
                        <option value="text"><?php esc_html_e('Text', 'ovoform');?></option>
                        <option value="textarea"><?php esc_html_e('Textarea', 'ovoform');?></option>
                        <option value="select"><?php esc_html_e('Select', 'ovoform');?></option>
                        <option value="checkbox"><?php esc_html_e('Checkbox', 'ovoform');?></option>
                        <option value="radio"><?php esc_html_e('Radio', 'ovoform');?></option>
                        <option value="file"><?php esc_html_e('File', 'ovoform');?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Is Required', 'ovoform');?></label>
                    <select name="is_required" class="form-control form--control" required>
                        <option value=""><?php esc_html_e('Select One', 'ovoform');?></option>
                        <option value="required"><?php esc_html_e('Required', 'ovoform');?></option>
                        <option value="optional"><?php esc_html_e('Optional', 'ovoform');?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Form Label', 'ovoform');?></label>
                    <input type="text" name="form_label" class="form-control form--control" required>
                </div>
                <div class="form-group extra_area">

                </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn--primary text-white w-100 generatorSubmit"><?php esc_html_e('Add', 'ovoform');?></button>
              </div>
          </form>
      </div>
    </div>
</div>