<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FormHandler {

	private $_purchase_repo;
	private $_notices;

	public function __construct(PurchaseRepo $repo, Notices $notices) {
		$this->_purchase_repo = $repo;
		$this->_notices = $notices;

		$this->init();
	}

	/**
	 * Hook in methods
	 */
	public function init() {
		add_action( 'init', array( $this, 'process_purchase_code' ), 20 );

		add_action( 'wp_ajax_ss_handle_purchase_code', array( $this, 'process_purchase_code' ) );
		add_action( 'wp_ajax_nopriv_ss_handle_purchase_code', array( $this, 'process_purchase_code' ) );

		add_action( 'wp_ajax_ss_delete_purchase_code', array( $this, 'process_delete_purchase_code' ) );
		add_action( 'wp_ajax_nopriv_ss_delete_purchase_code', array( $this, 'process_delete_purchase_code' ) );

		add_action( 'wp_ajax_ss_update_purchas_code', array( $this, 'process_update_code' ) );
		add_action( 'wp_ajax_nopriv_ss_update_purchas_code', array( $this, 'process_update_code' ) );
	}

	/**
	 * Process the purchase form.
	 */
	public function process_purchase_code() {
		if ( ! empty( $_POST['ss-envato-license'] ) ) {

			$code = sanitize_text_field( $_POST['ss-envato-license'] );

			$existed = $this->_purchase_repo->is_exists( $code );
			$purchase_info = $this->_purchase_repo->validate_code( $code );

			/*if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				pr($purchase_info);
			    die();
			}*/
			if( ! empty( $existed ) && isset( $existed[0]['user_id'] ) ) {
				$user = get_userdata( $existed[0]['user_id'] );
				$user_email = ss_hide_mail( $user->user_email );
				$msg = __('Sorry, this key is already in the database. Registered for email <strong>', 'ss-envato-api') . $user_email . '</strong>';
				$this->_notices->add_error( $msg );
			} else if( $purchase_info ) {
				if( $this->_purchase_repo->add_code( $code, $purchase_info ) ) {
					// Successfully added
					$this->_notices->add_success('Successfully added, now you can visit our forum');

					if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
						$this->_notices->add_success('Page will be reloaded in 5 seconds.<script type="text/javascript">setTimeout(function() { window.location.reload(); }, 5000)</script>');
					}
				} else {
					// Code already exists or something else
					$this->_notices->add_error('Code can not be added to the database');
				}
			} else {
				// Wrong validation code, please try again
				$this->_notices->add_error('Wrong validation code, please try again');
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->_notices->show_msgs();
			    die();
			}

			return;
		}
	}

	/**
	 * Process delete purchase form.
	 */
	public function process_delete_purchase_code() {
		if ( ! empty( $_GET['id'] ) ) {

			$id = (int) $_GET['id'];

			$codes = SS_Envato_API()->purchase_repo->get_user_codes();

			$code = array_filter($codes, function( $el ) use($id) {
				return $el['id'] == $id;
			});

			if( empty( $code ) ) {
				$msg = __('Sorry, you cannot remove this purchase code', 'ss-envato-api');
				$this->_notices->add_error( $msg );
			} else {
				if( $this->_purchase_repo->delete_code( $id ) ) {
					$this->_notices->add_success( 'Purchase code removed' );
				} else {
					$this->_notices->add_success( 'Purchase code cannot be removed' );
				}
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->_notices->show_msgs();
			    die();
			}

			return;
		}
	}

	/**
	 * Process update code
	 */
	public function process_update_code() {
		if ( ! empty( $_GET['code'] ) ) {

			$code = sanitize_text_field( $_GET['code'] );

			$existed = $this->_purchase_repo->is_exists( $code );
			$purchase_info = $this->_purchase_repo->validate_code( $code );

			/*if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				pr($purchase_info);
			    die();
			}*/
			if( empty( $existed ) || ! isset( $existed[0]['user_id'] ) ) {
				$msg = __('Sorry, this key is not in the database.', 'ss-envato-api');
				$this->_notices->add_error( $msg );
			} else if( $purchase_info ) {
				if( $this->_purchase_repo->update_code( $code, $purchase_info ) ) {
					// Successfully added
					$reload_msg = ' Page will be reloaded in 5 seconds.<script type="text/javascript">setTimeout(function() { window.location.reload(); }, 5000)</script>';
					if( ! ss_is_date_expired( $purchase_info['supported_until'] ) ) {
						$this->_notices->add_success('Successfully update, now your support date expires <strong>' . date('d M Y', strtotime( $purchase_info['supported_until'] )) . '</strong>' . $reload_msg );
					} else {
						$this->_notices->add_error('Entered purchase code already expired in <strong>' . date('d M Y', strtotime( $purchase_info['supported_until'] )) . '</strong>' );
					}
				} else {
					// Code already exists or something else
					$this->_notices->add_error('Code can not be updated in the database');
				}
			} else {
				// Wrong validation code, please try again
				$this->_notices->add_error('Wrong validation code, please try again');
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->_notices->show_msgs();
			    die();
			}

			return;
		}
	}

}