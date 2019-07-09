<?php
class WPMT_i18n {
	
	public function load_plugin_textdomain() {

        $plugin_rel_path = plugin_basename( WPMT_PATH ).'/languages';
        load_plugin_textdomain( 'wpmt', false, $plugin_rel_path );

	}

}
