<?php

namespace SSAdmin;

use SS\Notices;
use SS\PurchaseRepo;
use SS\API;
use SS_Envato_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class VerificationPage {

	static $instance;

	public $notices;
	public $purchase;


	// class constructor
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );

		$this->notices = SS_Envato_API()->notices;
		$this->purchase = SS_Envato_API()->purchase_repo;

		$this->handle_form();
	}
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Verify Purchase Code',
			'Verify Code',
			'manage_options',
			'envato-verify-codes',
			[ $this, 'plugin_settings_page' ]
		);

	}

	public function handle_form() {

		if( isset( $_POST['ss-purchase-code'] ) ) {
			$code = sanitize_text_field( $_POST['ss-purchase-code'] );

			if(empty($code)) return;

			$response = $this->purchase->validate_code( $code );
			//var_dump($response);
			if( $response ) {
				$message = '<strong>Item:</strong> ' . $response['item']['name'];
				$message .= '<br><strong>Buyer:</strong> ' . $response['buyer'];
				$message .= '<br><strong>Supported until:</strong> ' . $response['supported_until'];
				$this->notices->add_success( $message );
			} else {
				$this->notices->add_warning('Purchase code is not verified');
			}

		}

	}

	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>Verify purchase code</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox ">
								<div class="inside">
							        <form method="post">
							        	<p>Enter purchase code to verify:</p>
							        	<input type="text" name="ss-purchase-code" />
							        	<input type="submit" name="verify" id="verify" class="button button-primary button-large" value="Verify">
									</form>
									<?php $this->notices->show_msgs(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}
}