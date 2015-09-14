<?php
namespace SS;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Activator {

	public static function activate() {
		global $wpdb;
		$tcbbpv_db = $wpdb->prefix . "ss_purchase_codes";

		// create table
		if( $wpdb->get_var("SHOW TABLES LIKE '$tcbbpv_db'") != $tcbbpv_db ){
			
			$sql = "CREATE TABLE `".$tcbbpv_db."` (
				`id` int(7) NOT NULL,
				  `envato_id` int(7) NOT NULL,
				  `purchase_code` varchar(255) NOT NULL,
				  `status` varchar(20) NOT NULL,
				  `user_id` int(7) NOT NULL,
				  `api_response` text NOT NULL,
				  `support_amount` varchar(255) NOT NULL,
				  `supported_until` datetime NOT NULL
				);
			";
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			// Create Default Page
			$post = array(
			  'post_title'    => 'Envato License Verification',
			  'post_content'  => '[ss-envato-verifier]',
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_type'	  => 'page'
			);
			
			// Insert the post into the database
			wp_insert_post( $post );
			
		} // end table creation
	}

}
