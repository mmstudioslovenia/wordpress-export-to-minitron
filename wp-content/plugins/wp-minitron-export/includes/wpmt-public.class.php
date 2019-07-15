<?php
class WPMT_Public
{
    
    public function __construct()
    {
        $this->api_key = WPMT_Admin::get_option('api_key');
        $register_groups = WPMT_Admin::get_option('register_groups');
        $this->api = new WPMT_API(WPMT_Admin::get_option('api_user'), WPMT_Admin::get_option('api_key'));
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
    
    /*
    *  It's sending POST request to Minitron,
    *  to save subscriber directly to partners
    */
    public function ajax_add_subscriber()
    {
        $nonce = (isset($_POST['nonce']) && !empty($_POST['nonce'])) ? $_POST['nonce'] : false ;
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? strip_tags($_POST['email']) : false ;
        $groups = (isset($_POST['groups']) && !empty($_POST['groups'])) ? implode(',',array_values($_POST['groups'])) : false;
        $name = (isset($_POST['name']) && !empty($_POST['name'])) ? strip_tags($_POST['name']) : false ;
        $mobile = (isset($_POST['mobile']) && !empty($_POST['mobile'])) ? strip_tags($_POST['mobile']) : false ;
        if (!wp_verify_nonce($nonce, 'ajax-nonce'))
            die('Nope!'); 

        if ($email == false) {
            wp_send_json_error(__('Please insert your email address', 'wpmt'));
        }
        
        $subscriber = [
            'email' => $email,
            'name' => $name,
            'cat_id' => $groups,
            'tel1' => $mobile, // optional, if enabled in widget
            'name' => $name, // optional, if enabled in widget
            'check_existing_by' => 'email'
        ];
        
        $response = $this->api->set_partner($subscriber);
        
        print_r($response);
        
        wp_send_json_success($response);

    }
}
