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

	public function opened_forums() {

		$envato_ids = $this->purchases->get_user_envato_ids();

		if( empty($envato_ids) ) return;

		$forums = get_posts( array(
			'post_type' => 'forum',
			'meta_query' => array(
				array(
					'key' => '_ss_envato_items',
					'value' => implode(',', $envato_ids),
					'compare' => 'LIKE'
				)
			)
		) );

		wp_reset_postdata();

		if( ! is_wp_error( $forums ) && ! empty( $forums ) ) {
			return $forums;
		}

		return false;

	}

}
