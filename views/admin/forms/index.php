<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php $html = '<a href="' . esc_url(ovoform_route_link('admin.forms.create', pageName: 'ovoform_forms')) . '" class="btn btn--primary"><i class="las la-plus"></i> Add New</a>';
ovoform_admin_top($pageTitle,$html) ?>

<div class="row gy-4">
    <div class="col-lg-4 col-sm-6">
        <div class="widget widget-primary bg-white p-3">
            <div class="widget__top">
                <i class="fas fa-archive"></i>
                <p class="fw-bold"><?php esc_html_e('Total Form', 'ovoform'); ?></p>
            </div>
            <div class="widget__bottom mt-3">
                <h4 class="widget__number"><?php printf(esc_html__('%s', 'ovoform'),  esc_html($widget['total_forms'])); ?></h4>
                <a href="<?php echo esc_url(ovoform_route_link('admin.forms',pageName: 'ovoform_forms')); ?>" class="widget__btn"><span><?php esc_html_e('View All', 'ovoform'); ?></span><i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6">
        <div class="widget widget-secondary bg-white p-3">
            <div class="widget__top">
                <i class="fas fa-comment-alt"></i>
                <p class="fw-bold"><?php esc_html_e('Total Submission', 'ovoform'); ?></p>
            </div>
            <div class="widget__bottom mt-3">
                <h4 class="widget__number"><?php printf(esc_html__('%s', 'ovoform'),  esc_html($widget['total_submissions'])); ?></h4>
                <a href="<?php echo esc_url(ovoform_route_link('admin.forms.submissions',pageName: 'ovoform_forms')); ?>" class="widget__btn"><span><?php esc_html_e('View All', 'ovoform'); ?></span><i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6">
        <div class="widget widget-dark bg-white p-3">
            <div class="widget__top">
                <i class="fas fa-comment-alt"></i>
                <p class="fw-bold"><?php esc_html_e('Total Unread', 'ovoform'); ?></p>
            </div>
            <div class="widget__bottom mt-3">
                <h4 class="widget__number"><?php printf(esc_html__('%s', 'ovoform'),  esc_html($widget['total_unread'])); ?></h4>
                <a href="<?php echo esc_url(ovoform_route_link('admin.forms.unread.submissions',pageName: 'ovoform_forms')); ?>" class="widget__btn"><span><?php esc_html_e('View All', 'ovoform'); ?></span><i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="card-header">
        <h5><?php echo esc_html_e('Generated Forms', 'ovoform') ?></h5>
    </div>
    <div class="card-body p-0">
        <?php if (!ovoform_check_empty($forms->data)) : ?> 
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two custom-data-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('S/N', 'ovoform'); ?></th>
                        <th><?php esc_html_e('Short Codes', 'ovoform'); ?></th>
                        <th><?php esc_html_e('Action', 'ovoform'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms->data as $key=> $form) { 
                        $key++;
                        ?>
                        <tr>
                            <td><?php echo intval($key); ?></td>
                            <td><span class="copy-element">[<?php echo esc_html__('ovoform', 'ovoform') ?>-<?php esc_html_e('form id', 'ovoform'); ?>="<?php echo intval($form->id); ?>" <?php esc_html_e('title', 'ovoform'); ?>="<?php printf(esc_html__('%s', 'ovoform'),  esc_html($form->name) ); ?>"]</span></td>
                            <td>
                                <a href="<?php echo esc_url(ovoform_route_link('admin.forms.edit', pageName: 'ovoform_forms')); ?>&amp;id=<?php echo esc_html(intval($form->id)); ?>" class="btn btn-sm btn--primary ms-1">
                                    <i class="la la-pencil"></i> <?php esc_html_e('Edit', 'ovoform'); ?>
                                </a>
                                <button type="button" class="btn btn-sm btn--danger ms-1 formDelete" data-toggle="tooltip" data-original-title="<?php esc_attr_e('Delete', 'ovoform'); ?>" data-id="<?php echo esc_html(intval($form->id)); ?>">
                                    <i class="las la-trash"></i> <?php esc_html_e('Delete', 'ovoform'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php else :
                ovoform_include('admin/partials/empty',[
                    'button_url'=> esc_url(ovoform_route_link('admin.forms.create', pageName: 'ovoform_forms')),
                    'title'=>'No Form Generated Yet!'
                ]);
            endif
        ?>
       
    </div>
    <?php if ($forms->links) { ?>
        <div class="card-footer">
            <?php echo wp_kses($forms->links, ovoform_allowed_html()); ?>
        </div>
    <?php } ?>
</div>


<div id="formDeleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Message Delete Confirmation', 'ovoform'); ?></h5>
                <button type="button" class="close bg--danger text-white rounded" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="las la-times"></i></span>
                </button>
            </div>
            <form action="<?php echo esc_url(ovoform_route_link('admin.forms.delete', pageName: 'ovoform_forms')); ?>" method="POST">
                <?php ovoform_nonce_field('admin.forms.delete') ?>
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

<?php ovoform_admin_bottom() ?>
<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('.formDelete').click(function() {
            var modal = $('#formDeleteModal');
            modal.find('[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
    });
</script>

