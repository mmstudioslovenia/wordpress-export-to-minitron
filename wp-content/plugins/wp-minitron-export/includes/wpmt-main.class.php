<?php
class WP_Minitron
{
    private static $plugin_name = 'wp-minitron-export';
    private static $version = '1.0.1';

    public function __construct()
    {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        new WPMT_Actions();
    }

    private function load_dependencies()
    {
        require_once WPMT_PATH.'includes/wpmt-i18n.php';
        require_once WPMT_PATH.'includes/wpmt-admin.class.php';
        require_once WPMT_PATH.'includes/wpmt-public.class.php';
        require_once WPMT_PATH.'includes/wpmt-actions.class.php';
    }

    private function set_locale()
    {
        $plugin_i18n = new WPMT_i18n();
        add_action('plugins_loaded', array($plugin_i18n, 'load_plugin_textdomain'));
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new WPMT_Admin();
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
        add_action('admin_menu', array($plugin_admin, 'admin_menu'));
        add_action('admin_init', array($plugin_admin, 'admin_init'));
        add_action('widgets_init', array($plugin_admin, 'widgets_init'));
	    add_filter('plugin_row_meta', array($plugin_admin, 'add_setting_link_meta'), 10, 2 );

    }

    private function define_public_hooks()
    {
        $plugin_public = new WPMT_Public();
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));
        add_action('wp_ajax_mailerlite_add_subscriber', array($plugin_public, 'ajax_add_subscriber'));
        add_action('wp_ajax_nopriv_mailerlite_add_subscriber', array($plugin_public, 'ajax_add_subscriber'));

    }

    public static function get_name() {
        return self::$plugin_name;
    }

    public static function get_version() {
        return self::$version;
    }

}