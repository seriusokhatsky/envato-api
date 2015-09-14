<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Notices {

	public $notices;

	public $ignore_key = '';

	public function __construct() {
		$this->notices = array();

		$this->generate_ignore_key();

		add_action('admin_init', array($this, 'nag_ignore'));
		add_action('admin_notices', array($this, 'add_notice'), 50 );
	}

	public function add_msg($msg, $type, $global = false) {
		$this->notices[] = array(
			'msg' => $msg,
			'type' => $type,
			'global' => $global
		);

		$this->generate_ignore_key();
		
	}

	public function get_msgs( $globals = false  ) {
		if( $globals ) {
			return array_filter($this->notices, function($v) {
				return $v['global'];
			});
		}
		return $this->notices;
	}

	public function clear_msgs() {
		$this->notices = array();
	}

	public function show_msgs( $globals = false ) {
		$msgs = $this->get_msgs( $globals );

		if(!empty($msgs)) {
			echo '<ul class="msgs-list">';
			foreach ($msgs as $key => $msg) {
				echo '<li class="alert alert-' . $msg['type'] . '">' . $msg['msg'] . '</li>';
			}
			echo '</ul>';
		}

		$this->clear_msgs();
	}

	public function add_error($msg, $global = false) {
		$this->add_msg( $msg, 'error', $global );
	}

	public function add_warning($msg, $global = false) {
		$this->add_msg( $msg, 'warning', $global );
	}

	public function add_success($msg, $global = false) {
		$this->add_msg( $msg, 'success', $global );
	}

	public function add_notice() {
		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, $this->ignore_key) ) {
	        echo '<div class="updated"><p>'; 
	        $this->show_msgs( true );
	        printf(__('<a href="%1$s">Dismiss Notice</a>'), '?' . $this->ignore_key . '=0');
	        echo "</p></div>";
		}
	}

	public function nag_ignore() {
		global $current_user;
        $user_id = $current_user->ID;
        //delete_user_meta($user_id, $this->ignore_key);
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET[$this->ignore_key]) && '0' == $_GET[$this->ignore_key] ) {
			add_user_meta($user_id, $this->ignore_key, 'true', true);
		}
	}
	public function generate_ignore_key() {
		$this->ignore_key = md5( serialize( $this->get_msgs( true ) ) );
	}
}