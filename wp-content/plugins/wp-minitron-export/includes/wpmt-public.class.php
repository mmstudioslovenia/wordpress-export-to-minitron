<?php
class WPMT_Public
{
    public function __construct()
    {
    }


    public function enqueue_scripts()
    {
        wp_enqueue_style('wpmt-public-style', WPMT_URL . 'assets/public/css/wpmt.css', array(), WP_Minitron::get_version(), $media = 'all');
        wp_enqueue_script('wpmt-public-js', WPMT_URL.'assets/public/js/wpmt.js', array('jquery'), WP_Minitron::get_version(), true);
        wp_localize_script(
            'wpmt-public-js',
            'wpmt_ajax',
            array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            )
        );
    }
}
