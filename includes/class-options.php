<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Options {

	function get( $opt ) {
		global $ss_envato;
		return ( isset( $ss_envato[$opt] ) ) ? $ss_envato[$opt] : '';
	}

}