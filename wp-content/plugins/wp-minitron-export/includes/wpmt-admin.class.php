<?php
class WPMT_Admin
{
    public $api;
    public $is_connected;

    public function __construct()
    {
        $this->api = new WPMT_API(self::get_option('api_user'), self::get_option('api_key'));
        $this->is_connected = $this->api->is_connected();
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_style('wpmt-select2', WPMT_URL.'assets/admin/css/select2.min.css', array(), '4.0.3', $media = 'all');
        wp_enqueue_style('wpmt-admin', WPMT_URL.'assets/admin/css/wpmt_admin.css', array(), '4.0.3', $media = 'all');
        
        wp_enqueue_script('wpmt-select2', WPMT_URL.'assets/admin/js/select2.min.js', array('jquery'), '4.0.3', true);
        wp_enqueue_script('wpmt-admin-js', WPMT_URL.'assets/admin/js/wpmt_admin.js', array('jquery'), WP_Minitron::get_version(), true);
        wp_localize_script('wpmt-admin-js', 'wpmt_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'select_groups' => __('Select your groups', 'wpmt'),
            'select_user' => __('Enter user email or id', 'wpmt'),
            'error' => __('Something goes wrong. please try again', 'wpmt'),
            )
        );
    }

    public function admin_menu()
    {
        add_menu_page(
            __('WP Minitron', 'wpmt'),
            __('WP Minitron', 'wpmt'),
            'manage_options',
            'wpmt',
            array($this, 'main_menu'),
            '',
            76
        );

    }

    public function main_menu()
    {
        ?>
        <div class="wrap wpmt_wrap">
            <h1><?php _e('WP Minitron Integration', 'wpmt'); ?></h1>
            <?php
                settings_errors();
                $active_tab = isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'general';
            ?>
            <h2 class="nav-tab-wrapper" style="margin-bottom: 10px">
                <a href="?page=wpmt&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General Settings', 'wpmt') ?></a>
                <a href="?page=wpmt&tab=cron" class="nav-tab <?php echo $active_tab == 'cron' ? 'nav-tab-active' : ''; ?>"><?php _e('Cron Settings', 'wpmt') ?></a>
                <a href="?page=wpmt&tab=manually" class="nav-tab <?php echo $active_tab == 'manually' ? 'nav-tab-active' : ''; ?>"><?php _e('Manually', 'wpmt') ?></a>
                <a href="?page=wpmt&tab=logs" class="nav-tab <?php echo $active_tab == 'logs' ? 'nav-tab-active' : ''; ?>"><?php _e('Logs', 'wpmt') ?></a>
            </h2>
            <form method="post" action="options.php">
                <?php
                
                    settings_fields('wpmt_group');
                    
                    switch ($active_tab)
                    {
                        case 'general':
                            do_settings_sections('wpmt_general_page');
                            submit_button();
                            
                            $lists = $this->api->get_partners_cats();
                            include WPMT_PATH . 'includes/views/lists-overview.php';
                            
                        break;
                        case 'cron':
                            do_settings_sections('wpmt_cron_page');
                        break;
                        case 'manually':
                            do_settings_sections('wpmt_manually_page');
                            include WPMT_PATH . 'includes/views/manually-settings.php';
                        break;
                        case 'logs':
                            do_settings_sections('wpmt_logs_page');
                        break;
                    }
                ?>
            </form>
        <?php

    }

    public function admin_init()
    {
        register_setting(
            'wpmt_group', // option group
            'wpmt_options', // option name
            array($this, 'sanitize')  // sanitize callback
        );

        // Settings Sections
        add_settings_section(
            'wpmt_general',
            __('General Settings', 'wpmt'),
            null,
            'wpmt_general_page'
        );
        
        // Cron Section
        add_settings_section(
            'wpmt_cron',
            __('Cron Settings', 'wpmt'),
            null,
            'wpmt_cron_page'
        );
        
        // Cron Section
        add_settings_section(
            'wpmt_manually',
            __('Manually', 'wpmt'),
            null,
            'wpmt_manually_page'
        );
        
        // Logs Section
        add_settings_section(
            'wpmt_logs',
            __('Logs', 'wpmt'),
            null,
            'wpmt_logs_page'
        );
        
        // Settings fields
        add_settings_field(
            'wpmt_api_user', // ID
            __('Minitron API user', 'wpmt'), // Title
            array($this, 'api_user_callback'), // Callback
            'wpmt_general_page', // Page
            'wpmt_general' // Section
        );

        // Settings fields
        add_settings_field(
            'wpmt_api_key', // ID
            __('Minitron API Key', 'wpmt'), // Title
            array($this, 'api_key_callback'), // Callback
            'wpmt_general_page', // Page
            'wpmt_general' // Section
        );
        
        add_settings_field(
            'wpmt_api_status', // ID
            __('Status', 'wpmt'), // Title
            array($this, 'api_status_callback'), // Callback
            'wpmt_general_page', // Page
            'wpmt_general' // Section
        );
        
        
        add_settings_field(
            'wpmt_reg_group', // ID
            __('Partners lists', 'wpmt'), // Title
            array($this, 'register_group_callback'), // Callback
            'wpmt_general_page', // Page
            'wpmt_general' // Section
        );
    }
    
    public function api_user_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wpmt_options[api_user]" id="wpmt_api_user" value="%s"> <p class="description"></p>',
            self::get_option('api_user')
        );
    }

    public function api_key_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wpmt_options[api_key]" id="wpmt_api_key" value="%s"> <p class="description"></p>',
            self::get_option('api_key')
        );
    }
    
    public function api_status_callback()
    {
        if ($this->is_connected)
        {
            echo '<span class="status positive">CONNECTED</span>';
        } else {
            echo '<span class="status negative">NOT CONNECTED</span>';
        }
    }

    public function register_group_callback()
    {
        if (self::get_option('api_user') && self::get_option('api_key')) {
	        $all_groups = $this->api->get_partners_cats();
	        if ($all_groups) {
		        echo '<select name="wpmt_options[register_groups][]" id="register_groups" multiple="multiple" class="wpmt_select2">';
		        echo '<option value="" >'.__('Select groups', 'wpmt').'</option>';
		        foreach ($all_groups as $group) {
                    $selected = (in_array($group['id'], self::get_option('register_groups'))) ? ' selected="selected"' : '';
			        echo '<option value="'.$group['id'].'" '.$selected.'>'.$group['cat_title'].' ( '.$group->total.' '.__('User', 'wpmt').')</option>';
		        }
		        echo '</select>';
	        }
        } else {
            _e('Please insert your API details first.', 'wpmt');
        }

    }

    public static function get_option($option_name = 'all')
    {
        $options = get_option('wpmt_options');
        $api_user = (isset($options['api_user']) && !empty($options['api_user'])) ? $options['api_user'] : '';
        $api_key = (isset($options['api_key']) && !empty($options['api_key'])) ? $options['api_key'] : '';
        $register_groups = (isset($options['register_groups']) && !empty($options['register_groups'])) ? $options['register_groups'] : array();

        switch ($option_name) {
            case 'api_key':
                return $api_key;
                break;
            case 'api_user':
                return $api_user;
            break;    
            case 'register_groups':
                return $register_groups;
                break;
            default:
                return $options;
                break;
        }
    }
}
