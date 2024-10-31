<?php

namespace Ovoform\Includes;

class Activator
{
    public function activate()
    {
        if (!get_option('ovoform_installed')) {
            global $wp_rewrite, $wpdb;

            $sql = file_get_contents(OVOFORM_ROOT . 'db/database.sql');
            $sql = str_replace('{{prefix}}', $wpdb->prefix, $sql);
            $sql = str_replace('{{collate}}', $wpdb->get_charset_collate(), $sql);
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            update_option('ovoform_installed',1);
            $wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%postname%/');
        }

        update_option('ovoform_is_enabled',1);

        add_role('ovoform_forms', 'Ovoform Administrator');
        $role = get_role('administrator');
        $role->add_cap('ovoform_forms');

        flush_rewrite_rules();
    }

    public function deactivate()
    {
        remove_role('ovoform_forms');
        $role = get_role('administrator');
        $role->remove_cap('ovoform_forms');
        update_option('ovoform_is_enabled',0);
    }
}
