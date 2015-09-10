<?php
namespace SSAdmin;

use SSAdmin\Meta;
use SSAdmin\Options;
use SSAdmin\LicensesPage;
use SSAdmin\VerificationPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Admin {

	private $_meta;
	private $_options;

	public function __construct() {
		
		$this->_meta = new Meta();
		add_action( 'cmb2_init', array( $this->_meta, 'cmb2_metaboxes' ) );
		
		$this->_options = new Options();
		$this->_options->redux_init();

		$this->license_page();
		$this->verification_page();

	}

	public function license_page() {
		add_action( 'plugins_loaded', function () {
			LicensesPage::get_instance();
		} );
	}

	public function verification_page() {
		add_action( 'init', function () {
			VerificationPage::get_instance();
		} );
	}

}