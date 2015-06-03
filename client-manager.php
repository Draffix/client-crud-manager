<?php
/**
 * Author: Jaroslav Klimcik
 * Website: http://jerryklimcik.cz
 * Date: 8.12.2014
 */

/*
 * Plugin Name:       Simple Client Manager
 * Description:       Simple CRUD client manager
 * Version:           0.1
 * Author:            Jaroslav Klimcik
 * Author URI:        http://jerryklimcik.cz
 */

if (!defined('WPINC')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'includes/client-manager-dependency.php';

function run_client_manager() {

    function client_manager_options_install() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'klient';

        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    client_name VARCHAR(50) NOT NULL,
                    client_position VARCHAR(50) NOT NULL,
                    client_company VARCHAR(150) NOT NULL,
                    client_phone VARCHAR(20) NOT NULL,
                    client_email VARCHAR(100) NOT NULL,
                    created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    FULLTEXT idx (client_name, client_company)
                ); $charset_collate
            ";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

    }
    // run the install scripts upon plugin activation
    register_activation_hook(__FILE__, 'client_manager_options_install');


    $spmm = new Client_Manager_Dependency();
    $spmm->run();

}

run_client_manager();