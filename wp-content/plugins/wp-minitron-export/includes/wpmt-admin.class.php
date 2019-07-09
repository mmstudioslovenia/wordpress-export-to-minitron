<?php
class WPMT_Admin
{
    public $group_api;

    public function __construct()
    {
        
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_style('wpmt-select2', WPMT_URL.'assets/admin/css/select2.min.css', array(), '4.0.3', $media = 'all');
        wp_enqueue_style('wpmt-admin-style', WPMT_URL.'assets/admin/css/wpmt_admin.css', array(), WP_Minitron::get_version(), $media = 'all');

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
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAPCAMAAADTRh9nAAABEVBMVEUAAAAAn1AApUsAnFIAm00AoFAAoVQAoFQAoFMAo1MAoVQAoVMAoFQAoFQAoVQAoFQAoFMAoVQAoVMAoVQAoFIAoFMAoVMAoVQBoFICoFMDoVQEoVQIoVUIolcIo1gKo1gMo1gQpFoSpFkUpl4Wpl8XqGEZpl8Zp18Zp2AaqGIbqGIcqGIdqWMfqWQfqmUkrGgorWssrm0ysXI2snM4s3VIuYBJuYFKuYFPu4RVu4Vdv4xfwI5fwY9hwI5hwZBlxJRpxZZqxZdtxZVvxpiEzaaIzqiL0auQ0q6R07CY1raq3cGs3sSv3sSz4Mi44szI6NbL6tnN6trb8OTe8ebg8unh8unn9e3r9vD3/Pn6/fv7/fzb8Z5rAAAAFHRSTlMAEBEfISNPYWZmanLl6Ozw+Pn7/Kl/NtMAAACXSURBVBjTY2Dg5BNGBTxsDEwC4uiAl4EFSBoqi+qLIgSFQYKi3vYaIQqieqJoglKWEs5BVrLy1o5aCEG1CCWvMDdFPx/3UBUkQRmzABGdSFeXYBs0Qe0oJztbTbigeoSMSbiHrm+gp78BRFDMSFXSVFTU3EZO2sLBWBQiiA74sQgKcgEFhblZUQAjAwOLEAcDBmBmxxQDAOB8II05mE7DAAAAAElFTkSuQmCC',
            76
        );

    }

    public function widgets_init()
    {
        //register_widget('WPMT_Widget');
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
            </h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields('wpmt_group');
                    do_settings_sections('wpmt_general_page');
                    submit_button();
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
            'wpmt_reg_group', // ID
            __('Register Users Groups', 'wpmt'), // Title
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

    public function register_group_callback()
    {
        $api_key = self::get_option('api_key');
        if (1 > 2) {
        //if( isset($api_key) && !empty($api_key) ) {
	        $all_groups = $this->group_api->get();
	        if ($all_groups) {
		        echo '<select name="wpmt_options[register_groups][]" id="register_groups" multiple="multiple" class="wpmt_select2">';
		        echo '<option value="" >'.__('Select groups', 'wpmt').'</option>';
		        foreach ($all_groups as $group) {
			        $selected = (in_array($group->id, self::get_option('register_groups'))) ? ' selected="selected"' : '';
			        echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmt').')</option>';
		        }
		        echo '</select>';
	        }
        } else {
            _e('Please insert your API details first.', 'wpmt');
        }

    }

	public function add_setting_link_meta( $links, $file ) {
		if ( strpos( $file, 'wp-mailerlite-lite.php' ) !== false ) {
			$new_links = array(
				'donate' => '<a href="'.admin_url( 'admin.php?page=wpmt').'" >'.__('Settings', 'wpmt').'</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
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
