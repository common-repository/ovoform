<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php ovoform_admin_top($pageTitle); ?>

<div class="card p-0">
    <div class="card-header">
        <h5><?php esc_html_e('Submitted Messages', 'ovoform') ?></h5>
    </div>
    <div class="card-body p-0">
        <?php if (!ovoform_check_empty($submissions->data)) : ?> 
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two custom-data-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('S/N', 'ovoform'); ?></th>
                        <th><?php esc_html_e('Form Title', 'ovoform'); ?></th>
                        <th><?php esc_html_e('Send Time', 'ovoform'); ?></th>
                        <th><?php esc_html_e('Action', 'ovoform'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions->data as $key => $submission) {
                        $key++;
                    ?>
                        <tr>
                            <td><?php echo esc_html(intval($key)); ?></td>
                            <td><?php echo esc_html(ovoform_get_form_title_by_id($submission->form_info_id)); ?></td>
                            <td><?php echo esc_html(ovoform_show_date_time($submission->created_at)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(ovoform_route_link('admin.forms.submission.details', pageName: 'ovoform_forms_submissions')); ?>&amp;id=<?php echo esc_html(intval($submission->id)); ?>" class="btn btn-sm btn--primary ms-1">
                                    <i class="la la-desktop"></i> <?php esc_html_e('Details', 'ovoform'); ?>
                                </a>

                                <button type="button" class="btn btn-sm btn--danger ms-1 submissionDelete" data-toggle="tooltip" data-original-title="<?php esc_attr_e('Delete', 'ovoform'); ?>" data-id="<?php echo esc_attr($submission->id); ?>">
                                    <i class="las la-trash"></i> <?php esc_html_e('Delete', 'ovoform'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (ovoform_check_empty($submissions->data)) { ?>
                        <tr>
                            <td class="text-muted text-center" colspan="100%"><?php esc_html_e('No submission found yet', 'ovoform'); ?></td>
                        </tr>
                    <?php } ?>
                    
                </tbody>
            </table><!-- table end -->
        </div>
        <?php else :
                ovoform_include('admin/partials/empty',[
                    'title'=>'No Submission Found Yet!'
                ]);
            endif
        ?>
    </div>
    <?php if ($submissions->links) { ?>
        <div class="card-footer">
            <?php echo wp_kses($submissions->links, ovoform_allowed_html()); ?>
        </div>
    <?php } ?>
</div>

<div id="messageDeleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Message Delete Confirmation', 'ovoform'); ?></h5>
                <button type="button" class="close text-white bg--danger rounded" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="las la-times"></i></span>
                </button>
            </div>
            <form action="<?php echo esc_url(ovoform_route_link('admin.forms.submissions.delete', pageName: 'ovoform_forms')); ?>" method="POST">
                <?php ovoform_nonce_field('admin.forms.submissions.delete') ?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p><?php esc_html_e('Do you really want to delete', 'ovoform'); ?>?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary"><?php esc_html_e('Yes', 'ovoform'); ?></button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-dark"><?php esc_html_e('No', 'ovoform'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php ovoform_admin_bottom(); ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('.submissionDelete').click(function() {
            var modal = $('#messageDeleteModal');
            modal.find('[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
    });
</script>