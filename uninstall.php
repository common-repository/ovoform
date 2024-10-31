<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

function ovoform_uninstall(){
    global $wpdb;

    $tables = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}ovoform%'", ARRAY_N );
    if ( $tables ) {
        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table[0]}" );
        }
    }
    delete_option('ovoform_installed');
    delete_option('ovoform_is_enabled');

    flush_rewrite_rules();
}

ovoform_uninstall();