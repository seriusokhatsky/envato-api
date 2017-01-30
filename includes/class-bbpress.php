<?php

namespace SS;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BBpress { 

	public $user;
	public $options;
	public $purchases;

	function __construct(User $user, PurchaseRepo $purchases, Options $options) {
		if( ! class_exists( 'bbPress' ) ) return;

		$this->user = $user;
		$this->options = $options;
		$this->purchases = $purchases;

		add_action('template_redirect', array( $this, 'check_forum') );
	}

	public function check_forum() {
		// check forum page
		if( ! $this->options->get('redirect') ) return;
		$forum_id = bbp_get_forum_id();

		if( ! empty( $forum_id ) && ! $this->user->can_create_topic() ) {

			$verify_page_id = ss_get_verify_page_id();
			$verify_page = get_permalink( $verify_page_id );
			$login_page = wp_login_url();

			if( ! $this->user->is_logged() ) {
				wp_redirect( $login_page ); die();
			}

			wp_redirect( $verify_page );
			die();
		} 
	}


	function can_download_plugin() {

		// Can be downloaded for staffs and admin
		if( SS_Envato_API()->user->is_staff() ) return true;

		$current_user = wp_get_current_user(); 

		$tf_codes = get_user_tf_codes( $current_user->ID );
		$site_codes = get_on_site_licenses( $current_user->ID );

	 	$plugins_codes = explode(',', etheme_get_custom_field('tf_themes'));
	 	$for_themes = explode(',', etheme_get_custom_field('et_themes'));

	 	$intersection = array_intersect($tf_codes, $plugins_codes);
	 	$intersection2 = array_intersect($site_codes, $for_themes);

	 	if(count($intersection) > 0 || count($intersection2) > 0 || et_is_staff()) {
	 		return true;
	 	}

	 	return false;

	}

	public function opened_forums() {

		$envato_ids = $this->purchases->get_user_envato_ids();

		$licenses = $this->purchases->get_on_site_licenses();

		if( empty($envato_ids) && empty($licenses) ) return;

		$query = array( 'relation' => 'OR' );

		if( ! empty( $envato_ids ) ) {
			$query[] = array(
				'key' => '_ss_envato_items',
				'value' => implode(',', $envato_ids),
				'compare' => 'LIKE'
			);
		}
		if( ! empty( $licenses ) ) {
			$query[] = array(
				'key' => '_ss_onsite_items',
				'value' => $licenses,
				'compare' => 'LIKE'
			);
		}

		$forums = get_posts( array(
			'post_type' => 'forum',
			'meta_query' => $query
		) );

		wp_reset_postdata();

		if( ! is_wp_error( $forums ) && ! empty( $forums ) ) {
			return $forums;
		}

		return false;

	}

}
