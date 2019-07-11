<?php
/*
    Plugin Name: WordPress export users to Minitron
    Plugin URI: https://wordpress.org/plugins/wp-minitron-export/
    Description: With this plugin you can sync your website users with Minitron email marketing software
    Version: 1.0.1
    Author: Georgi Kolev
    Author URI: http://www.mmstudio.si
*/
define('WPMT_URL', plugin_dir_url( __FILE__ ));
define('WPMT_PATH', plugin_dir_path( __FILE__ ));

add_filter('cron_schedules', 'wpmt_sync_time');

function wpmt_sync_time($schedules)
{
    $schedules['wpmt_sync_time'] = array('interval' => 1800, 'display' => '30 minutes');
    return $schedules;
}

add_action('wpmt_sync_cron', 'wpmt_sync_task');

function wpmt_sync_task()
{
	require_once WPMT_PATH. 'includes/wpmt-actions.class.php';
    $WPMT_Actions = new WPMT_Actions();
	$WPMT_Actions->ajax_batch_update();
}

function wpmt_sync_timer()
{
    if (!wp_next_scheduled('wpmt_sync_cron')) {
        wp_schedule_event(time(), 'wpmt_sync_time', 'wpmt_sync_cron');
    }
}

function wpmt_activation()
{
	require_once WPMT_PATH. 'includes/wpmt-activator.class.php';
	WPMT_Activator::activate();
	wpmt_sync_timer();
}

register_activation_hook(__FILE__, 'wpmt_activation');

function wpmt_deactivation() 
{
	require_once WPMT_PATH. 'includes/wpmt-deactivator.class.php';
	WPMT_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'wpmt_deactivation');

require WPMT_PATH . 'includes/wpmt-main.class.php';

function run_wpmt() 
{
	new WP_Minitron();
}

run_wpmt();