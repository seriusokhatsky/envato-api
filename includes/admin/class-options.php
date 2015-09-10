<?php

namespace SSAdmin;

use Redux;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Options {

	public function redux_init() {
		if( ! class_exists( 'Redux' ) ) return;
		$opt_name = "ss_envato";

		$args = Array(
		    'opt_name'    => $opt_name,
		    'menu_type'	  => 'submenu',
		    'menu_title'  => 'Envato API',
		    // Any other arguments you wish to set.  To save space in this example
		    // please refer to the arguments documentation, or review the
		    // sample-config.php file
		);

		Redux::setArgs ($opt_name, $args);

		// Lets create a section with no subsections, a basic section, if you will.
		$section = array(
		    'title'  => 'Envato API',
		    'id'     => 'envato_api',
		    'desc'   => '',
		    'icon'   => 'el el-home',
		    'fields' => array(
		        array(
		            'id'       => 'token',
		            'type'     => 'text',
		            'title'    => 'Envato user token',
		            'subtitle' => '',
		            'desc'     => '',
		            'default'  => '',
		        ),    
		    )
		);

		Redux::setSection($opt_name, $section);
	}

	function get( $opt ) {
		global $ss_envato;
		return ( isset( $ss_envato[$opt] ) ) ? $ss_envato[$opt] : '';
	}

}