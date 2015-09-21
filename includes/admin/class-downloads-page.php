<?php

namespace SSAdmin;

use SS_Envato_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DownloadsPage {

	static $instance;

	public $notices;
	public $api;


	// class constructor
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );

		$this->notices = SS_Envato_API()->notices;
		$this->api = SS_Envato_API()->api;

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
			'Download envato items',
			'Download envato',
			'manage_options',
			'envato-downloads',
			[ $this, 'plugin_settings_page' ]
		);

	}

	public function handle_form() {

		if( ! empty( $_POST['ss-download-code'] ) ) {
			$code = strip_tags( trim( $_POST['ss-download-code'] ) );
			$download = $this->api->call('/private/user/download-purchase:' . $code . '.json', array(), 'v1');

			if( empty( $download['body'] ) ) return; 

			$data = json_decode($download['body'], true);
			if( ! empty( $data['download-purchase'] ) ) {
				$this->notices->add_msg('Downloaded purchase <a href="' . $data['download-purchase']['download_url'] . '">' . $code . '</a>');
			} else {
				$this->notices->add_msg('Wrong purchase code');
			}
		}

	}


	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>Available downloads</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox ">
								<div class="inside">
							        <form method="post">
							        	<p>Enter purchase code to download:</p>
							        	<input type="text" name="ss-download-code" />
							        	<input type="submit" name="download" id="download" class="button button-primary button-large" value="Download">
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