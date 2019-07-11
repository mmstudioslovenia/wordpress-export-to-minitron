<?php

class WPMT_Actions
{
    private $api_key;
    private $register_groups;
    private $groups_api;
    private $subscribers_api;

    public function __construct()
    {
        $this->api_key = WPMT_Admin::get_option('api_key');
        $register_groups = WPMT_Admin::get_option('register_groups');
        $this->api = new WPMT_API(WPMT_Admin::get_option('api_user'), WPMT_Admin::get_option('api_key'));
        $this->register_group_id = $register_groups[0];

        $this->define_hooks();
    }

    private function define_hooks()
    {
        add_action('wp_ajax_batch_update', array($this, 'ajax_batch_update'));
    }
    
    public function ajax_batch_update()
    {
        $users = get_users();
        $subscribers = [];
        
        foreach ($users as $user)
        {
            $subscriber = [
                'email' => $user->user_email,
                'name' => $user->display_name,
                'cat_id' => $this->register_group_id,
                'check_existing_by' => 'email',
            ];
            
            array_push($subscribers, $subscriber);
        }
        
        $this->api->set_partners($subscribers);
    }


}