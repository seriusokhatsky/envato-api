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
	}

	/**
	 * Process the purchase form.
	 */
	public function process_purchase_code() {
		if ( ! empty( $_POST['ss-envato-license'] ) ) {

			$code = sanitize_text_field( $_POST['ss-envato-license'] );

			$purchase_info = $this->_purchase_repo->validate_code( $code );


			/*if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				pr($purchase_info);
			    die();
			}*/

			if( $purchase_info ) {
				if( $this->_purchase_repo->add_code( $code, $purchase_info ) ) {
					// Successfully added
					$this->_notices->add_success('Successfully added');
				} else {
					// Code already exists or something else
					$this->_notices->add_error('Code already exists or something else');
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