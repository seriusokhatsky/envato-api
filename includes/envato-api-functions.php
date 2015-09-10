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