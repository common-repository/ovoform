<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<script>
    (function($) {
        "use strict"

        jQuery('[name=form_type]').on('change', function() {
            var formType = jQuery(this).val();
            var extraFields = formGenerator.extraFields(formType);
            jQuery('.extra_area').html(extraFields);
        }).change();


        jQuery(document).on('click', '.addOption', function() {
            var html = formGenerator.addOptions();
            jQuery('.options').append(html);
        });

        jQuery(document).on('click', '.removeOption', function() {
            jQuery(this).closest('.form-group').remove();
        });

        jQuery(document).on('click', '.editFormData', function() {
            formGenerator.formEdit(jQuery(this));
        });

        jQuery(document).on('click', '.removeFormData', function() {
            jQuery(this).closest('.col-md-4').remove();
        });

        jQuery('.form-generate-btn').on('click', function() {
            formGenerator.showModal();
        });


        var updateId = formGenerator.totalField;
        jQuery(formGenerator.formClassName).submit(function(e) {
            updateId += 1;
            e.preventDefault();
            var form = jQuery(this);
            var formItem = formGenerator.formsToJson(form);
            formGenerator.makeFormHtml(formItem, updateId);
            formGenerator.closeModal();
        });


        jQuery('input[name=currency]').on('input', function() {
            jQuery('.currency_symbol').text(jQuery(this).val());
        });

        jQuery(document).on('click', '.removeBtn', function() {
            jQuery(this).closest('.user-data').remove();
        });
    })(jQuery)
</script>