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