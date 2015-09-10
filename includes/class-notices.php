<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Notices {

	public $notices;

	public function __construct() {
		$this->notices = array();
	}

	public function add_msg($msg, $type) {
		$this->notices[] = array(
			'msg' => $msg,
			'type' => $type
		);
	}

	public function get_msgs() {
		return $this->notices;
	}

	public function clear_msgs() {
		$this->notices = array();
	}

	public function show_msgs() {
		$msgs = $this->get_msgs();

		if(!empty($msgs)) {
			echo '<ul class="msgs-list">';
			foreach ($msgs as $key => $msg) {
				echo '<li class="alert alert-' . $msg['type'] . '">' . $msg['msg'] . '</li>';
			}
			echo '</ul>';
		}

		$this->clear_msgs();
	}

	public function add_error($msg) {
		$this->add_msg( $msg, 'error' );
	}

	public function add_warning($msg) {
		$this->add_msg( $msg, 'warning' );
	}

	public function add_success($msg) {
		$this->add_msg( $msg, 'success' );
	}

}