<?php

/**
 * Author: Jaroslav Klimcik
 * Website: http://jerryklimcik.cz
 * Date: 8.12.2014
 */
class Client_Manager_Dependency {

    protected $loader;

    protected $plugin_slug;

    protected $version;

    public function __construct() {

        $this->plugin_slug = 'client-manager-slug';
        $this->version = '0.1';

        $this->load_dependencies();
        $this->define_admin_hooks();

    }

    private function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/client-manager-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'front/client-manager-front.php';

        require_once plugin_dir_path(__FILE__) . 'client-manager-loader.php';
        $this->loader = new Client_Manager_Loader();

    }

    private function define_admin_hooks() {

        $admin = new Client_Manager_Admin($this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_menu', $admin, 'add_admin_menu_client_list');
        $this->loader->add_action('admin_menu', $admin, 'add_admin_menu_client_create');
        $this->loader->add_action('admin_menu', $admin, 'add_admin_menu_client_update');

        $front = new Client_Manager_Front($this->get_version());
        $this->loader->add_filter('the_content', $front, 'add_content_to_the_front');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_version() {
        return $this->version;
    }

}