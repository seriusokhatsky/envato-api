<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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