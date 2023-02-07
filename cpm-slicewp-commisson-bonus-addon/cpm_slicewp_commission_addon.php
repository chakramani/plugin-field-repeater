<?php
/** 

* @package Akismet 

*/
/* 

Plugin Name: SliceWP Commission Bonus Addon

Plugin URI: http://codepixelzmedia.com.np// 

Description: Use to calculate the commission bonus monthly. 

Version: 1.0.0

Author: Codepixelzmedia
*/


/* Main Plugin File */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// define( 'PLUGIN_ROOT_DIR', plugin_dir_path( __FILE__ ) );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


if ( is_plugin_active( 'slicewp/index.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "cpm-slicewp-commisson-bonus-addon" . DIRECTORY_SEPARATOR  ."cpm_slicewp-commisson_loader.php";
	require_once $init_file;

} else {
	if ( ! function_exists( 'slicewp_addon_notification' ) ) {
		function slicewp_addon_notification() {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Please install and activate sliceWP plugin to use sliceWP Addon .', 'slicewp-addon' ); ?></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'slicewp_addon_notification' );
}

