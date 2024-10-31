<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<script>
    class FormGenerator {
        constructor(formClassName = null) {
            this.fieldType = null;
            this.totalField = 0;
            if (this.formClassName) {
                this.formClassName = '.' + formClassName;
            } else {
                this.formClassName = '.generate-form';
            }
        }

        extraFields(fieldType) {
                    this.fieldType = fieldType;
                    var addNew = '';
                    if (this.fieldType == 'file') {
                        var field = `<select class="select2-multi-select form-control" name="extensions" multiple>
                    <option value="jpg"><?php esc_html_e('JPG','ovoform') ?></option>
                    <option value="jpeg"><?php esc_html_e('JPEG','ovoform') ?></option>
                    <option value="png"><?php esc_html_e('PNG','ovoform') ?></option>
                    <option value="pdf"><?php esc_html_e('PDF','ovoform') ?></option>
                    <option value="doc"><?php esc_html_e('DOC','ovoform') ?></option>
                    <option value="docx"><?php esc_html_e('DOCX','ovoform') ?></option>
                    <option value="txt"><?php esc_html_e('TXT','ovoform') ?></option>
                    <option value="xlx"><?php esc_html_e('XLX','ovoform') ?></option>
                    <option value="xlsx"><?php esc_html_e('XLXS','ovoform') ?></option>
                    <option value="csv"><?php esc_html_e('CSV','ovoform') ?></option>
                </select>`;
                        var title = `<?php esc_html_e('File Extensions','ovoform') ?> <small class="text--danger">*</small> <small class="text-primary">(<?php esc_html_e('Separate each element by comma','ovoform') ?>)</small>`;
                    } else {
                        var field = `<input type="text" name="options[]" class="form-control" required>`;
                        addNew = `<button type="button" class="btn btn-sm btn--primary addOption"><i class="las la-plus me-0"></i></button>`;
                        var title = `<?php esc_html_e('Add Options','ovoform') ?>`;
                    }

                    var html = `
                <div class="d-flex justify-content-between flex-wrap">
                    <label>${title}</label>
                    ${addNew}
                </div>
                <div class="options mt-2">
                    <div class="form-group">
                        <div class="input-group">
                            ${field}
                        </div>
                    </div>
                </div>
            `;
            if (this.fieldType == 'text' || this.fieldType == 'textarea' || this.fieldType == '') {
                html = '';
            }

            return html;
        }

        addOptions() {
            return `
        <div class="form-group">
            <div class="input-group">
                <input type="text" name="options[]" class="form-control form--control" required>
                <button class="btn btn--danger input-group-text removeOption"><i class="las la-times"></i></button>
            </div>
        </div>
    `;
        }

        formsToJson(form) {
            var extensions = null;
            var options = [];
            this.fieldType = form.find('[name=form_type]').val();
            if (this.fieldType == 'file') {
                extensions = form.find('[name=extensions]').val();
            }

            if (this.fieldType == 'select' || this.fieldType == 'checkbox' || this.fieldType == 'radio') {
                var options = jQuery("[name='options[]']").map(function() {
                    return jQuery(this).val();
                }).get();
            }
            var formItem = {
                type: this.fieldType,
                is_required: form.find('[name=is_required]').val(),
                label: form.find('[name=form_label]').val(),
                extensions: extensions,
                options: options,
                old_id: form.find('[name=update_id]').val()
            };
            return formItem;
        }

        makeFormHtml(formItem, updateId) {
            if (formItem.old_id) {
                updateId = formItem.old_id;
            }
            var hiddenFields = `
        <input type="hidden" name="form_generator[is_required][]" value="${formItem.is_required}">
        <input type="hidden" name="form_generator[extensions][]" value="${formItem.extensions}">
        <input type="hidden" name="form_generator[options][]" value="${formItem.options}">
    `;
            var formsHtml = `
        ${hiddenFields}
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Label</label>
                <input type="text" name="form_generator[form_label][]" class="form-control form--control" value="${formItem.label}" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <input type="text" name="form_generator[form_type][]" class="form-control form--control" value="${formItem.type}" readonly>
            </div>
            <div class="btn-group w-100">
                <button type="button" class="btn btn--primary editFormData" data-form_item='${JSON.stringify(formItem)}' data-update_id="${updateId}"><i class="las la-pen"></i></button>
                <button type="button" class="btn btn--danger removeFormData"><i class="las la-times"></i></button>
            </div>
        </div>
    `;
            var html = `
        <div class="col-md-4">
            <div class="card border mb-3" id="${updateId}">
                ${formsHtml}
            </div>
        </div>
    `;

            if (formItem.old_id) {
                html = formsHtml;
                jQuery(`#${formItem.old_id}`).html(html);
            } else {
                jQuery('.addedField').append(html);
            }
        }

        formEdit(element) {
            this.showModal()

            var formItem = element.data('form_item');
            var form = jQuery(this.formClassName);
            form.find('[name=form_type]').val(formItem.type);

            form.find('[name=form_label]').val(formItem.label);

            form.find('[name=is_required]').val(formItem.is_required);
            form.find('[name=update_id]').val(element.data('update_id'))
            var html = '';
            if (formItem.type == 'file') {
                html += `
            <div class="d-flex justify-content-between flex-wrap">
                <label><?php esc_html_e('File Extensions','ovoform') ?> <small class="text--danger">*</small> <small class="text-primary">(<?php esc_html_e('Separate each element by comma','ovoform') ?>)</small></label>
            </div>
            <div class="mt-2">
                <div class="form-group">
                    <select class="select2-multi-select" name="extensions" multiple>
                        <option value="jpg"><?php esc_html_e('JPG','ovoform') ?></option>
                        <option value="jpeg"><?php esc_html_e('JPEG','ovoform') ?></option>
                        <option value="png"><?php esc_html_e('PNG','ovoform') ?></option>
                        <option value="pdf"><?php esc_html_e('PDF','ovoform') ?></option>
                        <option value="doc"><?php esc_html_e('DOC','ovoform') ?></option>
                        <option value="docx"><?php esc_html_e('DOCX','ovoform') ?></option>
                        <option value="txt"><?php esc_html_e('TXT','ovoform') ?></option>
                        <option value="xlx"><?php esc_html_e('XLX','ovoform') ?></option>
                        <option value="xlsx"><?php esc_html_e('XLSX','ovoform') ?></option>
                        <option value="csv"><?php esc_html_e('CSV','ovoform') ?></option>
                    </select>
                </div>
            </div>
        `;
            }
            var i = 0;
            var optionItem = '';

            //Show
            for (const [index, option] of Object.entries(formItem.options)) {
                //Show
                var isRemove = '';
                if (i != 0) {
                    isRemove = `
                    <button class="btn btn--danger input-group-text removeOption"><i class="las la-times"></i></button>
                `;
                }
                if (i == 0) {
                    html += `
                    <div class="d-flex justify-content-between flex-wrap">
                        <label>Add Options</label>
                        <button type="button" class="btn btn-sm btn--primary addOption"><i class="las la-plus me-0"></i></button>
                    </div>
                `;
                }
                i += 1;
                optionItem += `
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="options[]" value="${option}" class="form-control" required>
                        ${isRemove}
                    </div>
                </div>
            `;
            };
            // }

            if (formItem.type != 'file') {
                html += `
            <div class="options mt-2">
                ${optionItem}
            </div>
        `;
            }

            jQuery('.generatorSubmit').text('<?php esc_html_e('Update','ovoform') ?>');
            jQuery('.extra_area').html(html);
            jQuery('.extra_area').find('select').val(formItem.extensions);

        }


        resetAll() {
            jQuery(formGenerator.formClassName).trigger("reset");
            jQuery('.extra_area').html('');
            jQuery('.generatorSubmit').text('<?php esc_html_e('Add','ovoform') ?>');
            jQuery('[name=update_id]').val('');

        }

        closeModal() {
            var modal = jQuery('#formGenerateModal');
            modal.modal('hide');
        }

        showModal() {
            this.resetAll();
            var modal = jQuery('#formGenerateModal');
            modal.modal('show');
        }
    }
</script>