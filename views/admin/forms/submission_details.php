<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php ovoform_admin_top($pageTitle); ?>

<div class="card p-0">
    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="fw-bold"><?php esc_html_e('Key', 'ovoform'); ?></th>
                        <th class="fw-bold"><?php esc_html_e('Value', 'ovoform'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formData as $item) : ?>
                        <tr>
                            <td><?php printf(esc_html__('%s', 'ovoform'), esc_html($item['name'])); ?></td>
                            <td>
                                <?php
                                if (is_array($item['value'])) {
                                    $number = count($item['value']);
                                    for ($i = 0; $i < $number; $i++) {
                                        echo esc_html(' ' . $item['value'][$i] . ' ,');
                                    }
                                } else {
                                    if ($item['value']) {
                                        if ($item['type'] == 'file') { ?>
                                            <a href="<?php echo esc_url(ovoform_route_link('admin.forms.attachment.download', pageName: 'ovoform_forms')); ?>&amp;file=<?php echo esc_attr(ovoform_encrypt($item['value'])); ?>" class="me-3">
                                                <i class="fa fa-file"></i> <?php esc_html_e('Attachment', 'ovoform'); ?>
                                            </a>
                                    <?php } else {
                                            echo esc_html($item['value']);
                                        }
                                    } else {
                                        echo "N/A";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php ovoform_admin_bottom(); ?>