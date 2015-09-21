<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! function_exists( 'ss_check_code_before_process' ) ) {
	function ss_check_code_before_process( $code ) {
		$repo = SS_Envato_API()->purchase_repo;

		$result = array(
			'status' => 'error', //error or success
			'msg' => '', // exists | not valid | okay
			'data' => '' // purchase data
		);

		// Check if already in the database
		$existed = $repo->is_exists( $code );

		if( ! empty( $existed ) && isset( $existed[0]['user_id'] ) ) {
			$user = get_userdata( $existed[0]['user_id'] );
			$user_email = ss_hide_mail( $user->user_email );
			$result = array(
				'status' => 'error',
				'msg' => __('Sorry, this key is already in the database. Registered for email <strong>', 'ss-envato-api') . $user_email . '</strong>'
			);

			return $result;
		} else {
			$valid = $repo->validate_code( $code );
 
			if( $valid ) {
				$result = array(
					'status' => 'success',
					'data' => $valid
				);
			} else {
				$result = array(
					'status' => 'error',
					'msg' => __('Wrong validation code, please try again', 'ss-envato-api')
				);
			}
		}

		return $result;
	}
}

if( ! function_exists( 'ss_is_user_has_support' ) ) {
	function ss_is_user_has_support( $user_id ) {
		$purchase_repo	= new SS\PurchaseRepo( SS_Envato_API()->api );
		$purchase_repo->set_user_id( $user_id );

		if( $purchase_repo->is_active_support() ) {
			return true;
		}

		return false;
	}
}

if( ! function_exists( 'ss_is_date_expired' ) ) {
	function ss_is_date_expired( $datetime ) {
		$time_past = strtotime( $datetime );
		$time_now = time();

		return ( $time_now > $time_past );
	}
}

if( ! function_exists( 'ss_hide_mail' ) ) {
	function ss_hide_mail($email) {
	    $mail_segments = explode("@", $email);
	    $half = (int) strlen( $mail_segments[0] )/2;
	    $mail_segments[0] = str_repeat("*", $half) . substr($mail_segments[0], $half);

	    return implode("@", $mail_segments);
	}
}

if( ! function_exists( 'ss_verification_form' ) ) {
	function ss_verification_form() {

		ob_start();

		ss_purchase_code_form();

		$out = ob_get_contents();
		ob_clean();

		return $out;


	}
	add_shortcode( 'ss-envato-verifier', 'ss_verification_form' );
}

if( ! function_exists( 'ss_get_verify_page_id' ) ) {
	function ss_get_verify_page_id() {
		global $wpdb;
	    $results = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_content LIKE '%[ss-envato-verifier]%'");
	    if( ! empty( $results ) ) {
	    	return $results[0]->ID;
	    } 
	    return 0;
	}
}