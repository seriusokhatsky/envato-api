<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class User {

	private $_is_logged = false;

	private $_user_id = 0;

	private $_is_staff = false;

	private $_purchase_repo;

	public function __construct(PurchaseRepo $purchase_repo) {

		$this->_purchase_repo = $purchase_repo;

		$this->set_login();

		if( $this->is_logged() ) {
			$this->set_user_id();

			$this->_purchase_repo->set_user_id( $this->get_user_id() );
			
			$this->set_staff();
		}

	}

	public function has_support() {		

		// Not logged in users don't have support
		if( ! $this->is_logged() ) {
			return false;
		}

		// Check is users purchase codes has not expired purchases for support
		if( $this->_purchase_repo->is_active_support() ) {
			return true;
		}

		return false;
	}

	public function can_create_topic() {

		// Not logged in users can't create topics
		if( ! $this->is_logged() ) {
			return false;
		}

		// Moderators and administrators can create topics
		if( $this->is_staff() ) {
			return true;
		}

		// Users that have support from themeforest can create topics
		if( $this->has_support() ) {
			return true;
		}

		return false;
	}

	public function set_login() {
		$this->_is_logged = is_user_logged_in();
	}

	public function is_logged() {
		return $this->_is_logged;
	}

	public function set_staff() {
		if( ! function_exists( 'bbp_get_user_role' ) ) {
			$this->_is_staff = false;
			return;
		}
		$this->_is_staff = ( bbp_get_user_role($this->get_user_id()) == 'bbp_keymaster' );
	}

	public function is_staff() {
		return $this->_is_staff;
	}

	public function set_user_id() {
		$this->_user_id = get_current_user_id();
	}

	public function get_user_id() {
		return $this->_user_id;
	}
}
