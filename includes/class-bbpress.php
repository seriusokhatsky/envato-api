<?php

namespace SS;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BBpress { 

	public $user;

	function __construct(User $user) {
		if( ! class_exists( 'bbPress' ) ) return;

		$this->user = $user;

		add_action('template_redirect', array( $this, 'check_forum') );

	}

	public function check_forum() {
		// check forum page
		$forum_id = bbp_get_forum_id();
		if( ! empty( $forum_id ) && ! $this->user->can_create_topic() ) {

			$verify_page = get_permalink( 11 );
			$login_page = wp_login_url();

			if( ! $this->user->is_logged() ) {
				wp_redirect( $login_page ); die();
			}

			wp_redirect( $verify_page );
			die();
		} 
	}

}
