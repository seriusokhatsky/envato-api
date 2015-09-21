<?php
namespace SSAdmin;

use SSAdmin\Meta;
use SSAdmin\LicensesPage;
use SSAdmin\VerificationPage;
use SSAdmin\DownloadsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Admin {

	private $_meta;
	private $_options;

	public function __construct() {
		
		$this->_meta = new Meta();
		add_action( 'cmb2_init', array( $this->_meta, 'cmb2_metaboxes' ) );

		$this->license_page();
		$this->verification_page();
		$this->downloads_page();

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

	public function downloads_page() {
		add_action( 'init', function () {
			DownloadsPage::get_instance();
		} );
	}

}